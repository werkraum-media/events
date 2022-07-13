<?php

namespace Wrm\Events\Service;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Resource\Exception\FolderDoesNotExistException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Index\MetaDataRepository;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use Wrm\Events\Domain\Model\Category;
use Wrm\Events\Domain\Model\Date;
use Wrm\Events\Domain\Model\Event;
use Wrm\Events\Domain\Model\Import;
use Wrm\Events\Domain\Model\Organizer;
use Wrm\Events\Domain\Model\Region;
use Wrm\Events\Domain\Repository\CategoryRepository;
use Wrm\Events\Domain\Repository\DateRepository;
use Wrm\Events\Domain\Repository\EventRepository;
use Wrm\Events\Domain\Repository\OrganizerRepository;
use Wrm\Events\Service\DestinationDataImportService\DataFetcher;
use Wrm\Events\Service\DestinationDataImportService\DatesFactory;

class DestinationDataImportService
{
    /**
     * @var Import
     */
    private $import;

    /**
     * @var Event
     */
    private $tmpCurrentEvent;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @var OrganizerRepository
     */
    private $organizerRepository;

    /**
     * @var DateRepository
     */
    private $dateRepository;

    /**
     * @var CategoryRepository
     */
    private $sysCategoriesRepository;

    /**
     * @var MetaDataRepository
     */
    private $metaDataRepository;

    /**
     * @var ConfigurationManager
     */
    private $configurationManager;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var PersistenceManager
     */
    private $persistenceManager;

    /**
     * @var DataFetcher
     */
    private $dataFetcher;

    /**
     * @var DatesFactory
     */
    private $datesFactory;

    /**
     * ImportService constructor.
     * @param EventRepository $eventRepository
     * @param OrganizerRepository $organizerRepository
     * @param DateRepository $dateRepository
     * @param CategoryRepository $sysCategoriesRepository
     * @param MetaDataRepository $metaDataRepository
     * @param ConfigurationManager $configurationManager
     * @param PersistenceManager $persistenceManager
     * @param ObjectManager $objectManager
     * @param DataFetcher $dataFetcher
     */
    public function __construct(
        EventRepository $eventRepository,
        OrganizerRepository $organizerRepository,
        DateRepository $dateRepository,
        CategoryRepository $sysCategoriesRepository,
        MetaDataRepository $metaDataRepository,
        ConfigurationManager $configurationManager,
        PersistenceManager $persistenceManager,
        ObjectManager $objectManager,
        DataFetcher $dataFetcher,
        DatesFactory $datesFactory
    ) {
        $this->eventRepository = $eventRepository;
        $this->organizerRepository = $organizerRepository;
        $this->dateRepository = $dateRepository;
        $this->sysCategoriesRepository = $sysCategoriesRepository;
        $this->metaDataRepository = $metaDataRepository;
        $this->configurationManager = $configurationManager;
        $this->persistenceManager = $persistenceManager;
        $this->objectManager = $objectManager;
        $this->dataFetcher = $dataFetcher;
        $this->datesFactory = $datesFactory;
    }

    public function import(
        Import $import
    ): int {
        $this->import = $import;

        // Get configuration
        $frameworkConfiguration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );

        // Set storage pid
        $persistenceConfiguration = [
            'persistence' => [
                'storagePid' => $this->import->getStoragePid(),
            ],
        ];

        // Set Configuration
        $this->configurationManager->setConfiguration(array_merge($frameworkConfiguration, $persistenceConfiguration));
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        $this->logger->info('Starting Destination Data Import Service');

        try {
            $data = $this->dataFetcher->fetchSearchResult($import);
        } catch (\Exception $e) {
            $this->logger->error('Could not receive data.');
            return 1;
        }

        return $this->processData($data);
    }

    public function processData(array $data): int
    {
        $this->logger->info('Processing json ' . count($data['items']));

        // Get selected region
        $selectedRegion = $this->import->getRegion();

        foreach ($data['items'] as $event) {
            $this->logger->info('Processing event ' . substr($event['title'], 0, 20));

            // Event already exists? If not create one!
            $this->tmpCurrentEvent = $this->getOrCreateEvent($event['global_id'], $event['title']);

            // Set language UID
            $this->tmpCurrentEvent->setLanguageUid(-1);

            // Set selected Region
            if ($selectedRegion instanceof Region) {
                $this->tmpCurrentEvent->setRegion($selectedRegion);
            }

            // Set Title
            $this->tmpCurrentEvent->setTitle(substr($event['title'], 0, 254));

            // Set Highlight (Is only set in rest if true)
            if ($event['highlight'] ?? false) {
                $this->tmpCurrentEvent->setHighlight($event['highlight']);
            }

            // Set Texts
            if ($event['texts'] ?? false) {
                $this->setTexts($event['texts']);
            }

            // Set address and geo data
            if (
                ($event['name'] ?? false)
                || ($event['street'] ?? false)
                || ($event['city'] ?? false)
                || ($event['zip'] ?? false)
                || ($event['country'] ?? false)
                || ($event['web'] ?? false)
            ) {
                $this->setAddress($event);
            }

            // Set LatLng
            if (
                ($event['geo']['main']['latitude'] ?? false)
                && ($event['geo']['main']['longitude'] ?? false)
            ) {
                $this->setLatLng($event['geo']['main']['latitude'], $event['geo']['main']['longitude']);
            }

            // Set Categories
            if ($event['categories'] ?? false) {
                $this->setCategories($event['categories']);
            }

            // Set Organizer
            if ($event['addresses'] ?? false) {
                $this->setOrganizer($event['addresses']);
            }

            // Set Social
            if ($event['media_objects'] ?? false) {
                $this->setSocial($event['media_objects']);
            }

            // Set Tickets
            if ($event['media_objects'] ?? false) {
                $this->setTickets($event['media_objects']);
            }

            // Set Dates
            if ($event['timeIntervals'] ?? false) {
                $this->setDates(
                    $event['timeIntervals'],
                    (bool) $this->getAttributeValue($event, 'DETAILS_ABGESAGT')
                );
            }

            // Set Assets
            if ($event['media_objects'] ?? false) {
                $this->setAssets($event['media_objects']);
            }

            // Update and persist
            $this->logger->info('Persist database');
            $this->eventRepository->update($this->tmpCurrentEvent);
            $this->persistenceManager->persistAll();
        }
        $this->doSlugUpdate();
        $this->logger->info('Finished import');
        return 0;
    }

    /**
     *
     * @param array $categories
     */
    private function setCategories(array $categories): void
    {
        $sysParentCategory = $this->import->getCategoryParent();
        if (!$sysParentCategory instanceof Category) {
            return;
        }

        foreach ($categories as $categoryTitle) {
            $tmpSysCategory = $this->sysCategoriesRepository->findOneByTitle($categoryTitle);
            if (!$tmpSysCategory) {
                $this->logger->info('Creating new category: ' . $categoryTitle);
                $tmpSysCategory = $this->objectManager->get(Category::class);
                $tmpSysCategory->setTitle($categoryTitle);
                $tmpSysCategory->setParent($sysParentCategory);
                $tmpSysCategory->setPid($this->import->getCategoriesPid());
                $this->sysCategoriesRepository->add($tmpSysCategory);
                $this->tmpCurrentEvent->addCategory($tmpSysCategory);
            } else {
                $this->tmpCurrentEvent->addCategory($tmpSysCategory);
            }
        }
    }

    private function setDates(
        array $timeIntervals,
        bool $canceled
    ): void {
        // @TODO: does not seem to work -->
        //$currentEventDates = $this->tmpCurrentEvent->getDates();
        //$this->tmpCurrentEvent->removeAllDates($currentEventDates);
        // <--
        // TODO: Workaround delete dates
        $currentEventDates = $this->tmpCurrentEvent->getDates();
        $this->logger->info('Found ' . count($currentEventDates) . ' to delete');
        foreach ($currentEventDates as $currentDate) {
            $this->dateRepository->remove($currentDate);
        }

        $dates = $this->datesFactory->createDates($timeIntervals, $canceled);
        foreach ($dates as $date) {
            $this->tmpCurrentEvent->addDate($date);
        }

        $this->logger->info('Finished setup dates');
    }

    private function setOrganizer(array $addresses): void
    {
        foreach ($addresses as $address) {
            if ($address['rel'] == "organizer") {
                $tmpOrganizer = $this->organizerRepository->findOneByName($address['name']);
                if ($tmpOrganizer) {
                    $this->tmpCurrentEvent->setOrganizer($tmpOrganizer);
                    continue;
                }
                $tmpOrganizer = $this->objectManager->get(Organizer::class);
                $tmpOrganizer->setLanguageUid(-1);
                $tmpOrganizer->setName($address['name'] ?? '');
                $tmpOrganizer->setCity($address['city'] ?? '');
                $tmpOrganizer->setZip($address['zip'] ?? '');
                $tmpOrganizer->setStreet($address['street'] ?? '');
                $tmpOrganizer->setPhone($address['phone'] ?? '');
                $tmpOrganizer->setWeb($address['web'] ?? '');
                $tmpOrganizer->setEmail($address['email'] ?? '');
                $tmpOrganizer->setDistrict($address['district'] ?? '');
                $this->organizerRepository->add($tmpOrganizer);
                $this->tmpCurrentEvent->setOrganizer($tmpOrganizer);
            }
        }
    }

    /**
     * @param array $event
     */
    private function setAddress(array $event): void
    {
        $this->tmpCurrentEvent->setName($event['name'] ?? '');
        $this->tmpCurrentEvent->setStreet($event['street'] ?? '');
        $this->tmpCurrentEvent->setCity($event['city'] ?? '');
        $this->tmpCurrentEvent->setZip($event['zip'] ?? '');
        $this->tmpCurrentEvent->setCountry($event['country'] ?? '');
        $this->tmpCurrentEvent->setPhone($event['phone'] ?? '');
        $this->tmpCurrentEvent->setWeb($event['web'] ?? '');
    }

    /**
     * @param array $media
     */
    private function setSocial(array $media): void
    {
        foreach ($media as $link) {
            if ($link['rel'] == "socialmedia" && $link['value'] == "Facebook") {
                $this->tmpCurrentEvent->setFacebook($link['url']);
            }
            if ($link['rel'] == "socialmedia" && $link['value'] == "YouTube") {
                $this->tmpCurrentEvent->setYouTube($link['url']);
            }
            if ($link['rel'] == "socialmedia" && $link['value'] == "Instagram") {
                $this->tmpCurrentEvent->setInstagram($link['url']);
            }
        }
    }

    private function setTickets(array $media): void
    {
        foreach ($media as $link) {
            if (isset($link['rel']) === false) {
                continue;
            }

            if ($link['rel'] === 'ticket') {
                $this->tmpCurrentEvent->setTicket($link['url']);
                return;
            }

            if (
                $link['rel'] === 'booking'
                && !$this->hasRelation('ticket', $media)
            ) {
                $this->tmpCurrentEvent->setTicket($link['url']);
                return;
            }

            if (
                $link['rel'] === 'PRICE_KARTENLINK'
                && !$this->hasRelation('ticket', $media)
                && !$this->hasRelation('booking', $media)
            ) {
                $this->tmpCurrentEvent->setTicket($link['url']);
                return;
            }
        }
    }

    private function hasRelation(string $needle, array $haystack): bool
    {
        foreach ($haystack as $key => $value) {
            if (isset($haystack['rel']) && $haystack['rel'] === $needle) {
                return true;
            }

            if (is_array($value) && $this->hasRelation($needle, $value)) {
                return true;
            }
        }

        return false;
    }

    private function setLatLng(string $lat, string $lng): void
    {
        $this->tmpCurrentEvent->setLatitude($lat);
        $this->tmpCurrentEvent->setLongitude($lng);
    }

    private function setTexts(array $texts): void
    {
        foreach ($texts as $text) {
            if (isset($text['value']) === false) {
                continue;
            }

            if ($text['rel'] == "details" && $text['type'] == "text/plain") {
                $this->tmpCurrentEvent->setDetails(str_replace('\n\n', '\n', $text['value']));
            }
            if ($text['rel'] == "teaser" && $text['type'] == "text/plain") {
                $this->tmpCurrentEvent->setTeaser(str_replace('\n\n', '\n', $text['value']));
            }
            if ($text['rel'] == "PRICE_INFO" && $text['type'] == "text/plain") {
                $this->tmpCurrentEvent->setPriceInfo(str_replace('\n\n', '\n', $text['value']));
            }
        }
    }

    private function getOrCreateEvent(string $globalId, string $title): Event
    {
        $event = $this->eventRepository->findOneByGlobalId($globalId);

        if ($event instanceof Event) {
            $this->logger->info(
                'Found "' . substr($title, 0, 20) . '..." with global id ' . $globalId . ' in database'
            );
            return $event;
        }

        // New event is created
        $this->logger->info(substr($title, 0, 20) . ' does not exist');
        $event = $this->objectManager->get(Event::class);
        // Create event and persist
        $event->setGlobalId($globalId);
        $event->setCategories(new ObjectStorage());
        $this->eventRepository->add($event);
        $this->persistenceManager->persistAll();
        $this->logger->info(
            'Not found "' . substr($title, 0, 20) . '..." with global id ' . $globalId . ' in database.'
            . ' Created new one.'
        );
        return $event;
    }

    private function setAssets(array $assets): void
    {
        $this->logger->info("Set assets");

        $importFolder = $this->import->getFilesFolder();

        $error = false;

        foreach ($assets as $media_object) {
            if ($media_object['rel'] == "default" && $media_object['type'] == "image/jpeg") {
                $fileUrl = urldecode($media_object['url']);
                $orgFileNameSanitized = $importFolder->getStorage()->sanitizeFileName(
                    basename(
                        urldecode($media_object['url'])
                    )
                );

                $this->logger->info('File attached:' . $fileUrl);
                $this->logger->info('File attached sanitized:' . $orgFileNameSanitized);

                if ($importFolder->hasFile($orgFileNameSanitized)) {
                    $this->logger->info('File already exists');
                } else {
                    $this->logger->info("File don't exist " . $orgFileNameSanitized);
                    // Load the file
                    if ($filename = $this->loadFile($fileUrl)) {
                        // Move file to defined folder
                        $this->logger->info('Adding file ' . $filename);

                        $importFolder->addFile($filename, basename($fileUrl));
                    } else {
                        $error = true;
                    }
                }

                if ($error !== true) {
                    if ($this->tmpCurrentEvent->getImages()->count() > 0) {
                        $this->logger->info('Relation found');
                    // TODO: How to delete file references?
                    } else {
                        $this->logger->info('No relation found');
                        if ($importFolder->hasFile($orgFileNameSanitized) === false) {
                            $this->logger->warning('Could not find file.', [$orgFileNameSanitized]);
                            continue;
                        }

                        $file = $importFolder->getStorage()->getFileInFolder($orgFileNameSanitized, $importFolder);
                        if (!$file instanceof File) {
                            $this->logger->warning('Could not find file.', [$orgFileNameSanitized]);
                            continue;
                        }

                        $this->metaDataRepository->update(
                            $file->getUid(),
                            [
                                'title' => $this->getShortenedString($media_object['value'], 100),
                                'description' => $media_object['description'] ?? '',
                                'alternative' => 'DD Import'
                            ]
                        );
                        $this->createFileRelations(
                            $file->getUid(),
                            'tx_events_domain_model_event',
                            $this->tmpCurrentEvent->getUid(),
                            'images',
                            $this->import->getStoragePid()
                        );
                    }
                }
            }
            $error = false;
        }
    }

    private function loadFile(string $fileUrl): string
    {
        $this->logger->info('Getting file ' . $fileUrl);

        $file = new \SplFileInfo($fileUrl);
        $temporaryFilename = GeneralUtility::tempnam($file->getBasename());

        $response = $this->dataFetcher->fetchImage($fileUrl);
        $fileContent = $response->getBody()->__toString();
        if ($response->getStatusCode() !== 200) {
            $this->logger->error('Cannot load file ' . $fileUrl);
        }

        if (GeneralUtility::writeFile($temporaryFilename, $fileContent, true) === false) {
            $this->logger->error('Could not write temporary file.');
            return '';
        }

        return $temporaryFilename;
    }

    private function createFileRelations(
        int $uid_local,
        string $tablenames,
        int $uid_foreign,
        string $fieldname,
        int $storagePid
    ): bool {
        $newId = 'NEW1234';

        $data = array();
        $data['sys_file_reference'][$newId] = array(
            'table_local' => 'sys_file',
            'uid_local' => $uid_local,
            'tablenames' => $tablenames,
            'uid_foreign' => $uid_foreign,
            'fieldname' => $fieldname,
            'pid' => $storagePid
        );

        $data[$tablenames][$uid_foreign] = array(
            'pid' => $storagePid,
            $fieldname => $newId
        );

        $dataHandler = $this->objectManager->get(DataHandler::class);
        $dataHandler->start($data, array());
        $dataHandler->process_datamap();

        if (count($dataHandler->errorLog) === 0) {
            return true;
        }

        foreach ($dataHandler->errorLog as $error) {
            $this->logger->info($error);
        }
        return false;
    }

    private function doSlugUpdate(): void
    {
        $this->logger->info('Update slugs');

        $slugHelper = GeneralUtility::makeInstance(
            SlugHelper::class,
            'tx_events_domain_model_event',
            'slug',
            $GLOBALS['TCA']['tx_events_domain_model_event']['columns']['slug']['config']
        );

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_events_domain_model_event');
        $queryBuilder = $connection->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeAll();

        $statement = $queryBuilder->select('uid', 'global_id')
            ->from('tx_events_domain_model_event')
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('slug', $queryBuilder->createNamedParameter('', \PDO::PARAM_STR)),
                    $queryBuilder->expr()->isNull('slug')
                )
            )
            ->execute();

        while ($record = $statement->fetch()) {
            if (is_array($record) === false) {
                continue;
            }

            $queryBuilder = $connection->createQueryBuilder();
            $queryBuilder->update('tx_events_domain_model_event')
                ->where(
                    $queryBuilder->expr()->eq(
                        'uid',
                        $queryBuilder->createNamedParameter($record['uid'], \PDO::PARAM_INT)
                    )
                )
                ->set('slug', $slugHelper->sanitize((string)$record['global_id']));
            $queryBuilder->getSQL();
            $queryBuilder->execute();
        }
    }

    /**
     * Fetch the boolean value for requested attribute.
     *
     * Returns first if multiple attributes with same key exist.
     * Casts "true" and "false" to true and false.
     */
    private function getAttributeValue(
        array $event,
        string $attributeKey
    ): bool {
        $attributes = array_filter($event['attributes'] ?? [], function (array $attribute) use ($attributeKey) {
            $currentKey = $attribute['key'] ?? '';
            return $currentKey === $attributeKey;
        });

        if ($attributes === []) {
            return false;
        }

        $value = $attributes[0]['value'] ?? null;

        if ($value === 'true') {
            return true;
        }
        if ($value === 'false') {
            return false;
        }

        return (bool) $value;
    }

    private function getShortenedString(string $string, int $lenght): string
    {
        if ($string === mb_substr($string, 0, $lenght)) {
            return $string;
        }

        return mb_substr($string, 0, $lenght - 3) . ' …';
    }
}
