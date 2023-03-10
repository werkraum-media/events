<?php

namespace Wrm\Events\Service;

use Exception;
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
use Wrm\Events\Service\DestinationDataImportService\CategoriesAssignment;
use Wrm\Events\Service\DestinationDataImportService\CategoriesAssignment\Import as CategoryImport;
use Wrm\Events\Service\DestinationDataImportService\DataFetcher;
use Wrm\Events\Service\DestinationDataImportService\DatesFactory;
use Wrm\Events\Service\DestinationDataImportService\LocationAssignment;
use Wrm\Events\Service\DestinationDataImportService\Slugger;

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
     * @var CategoriesAssignment
     */
    private $categoriesAssignment;

    /**
     * @var LocationAssignment
     */
    private $locationAssignment;

    /**
     * @var Slugger
     */
    private $slugger;

    /**
     * ImportService constructor.
     * @param EventRepository $eventRepository
     * @param OrganizerRepository $organizerRepository
     * @param DateRepository $dateRepository
     * @param MetaDataRepository $metaDataRepository
     * @param ConfigurationManager $configurationManager
     * @param PersistenceManager $persistenceManager
     * @param ObjectManager $objectManager
     * @param DataFetcher $dataFetcher
     * @param CategoriesAssignment $categoriesAssignment
     * @param LocationAssignment $locationAssignment
     * @param Slugger $slugger
     */
    public function __construct(
        EventRepository $eventRepository,
        OrganizerRepository $organizerRepository,
        DateRepository $dateRepository,
        MetaDataRepository $metaDataRepository,
        ConfigurationManager $configurationManager,
        PersistenceManager $persistenceManager,
        ObjectManager $objectManager,
        DataFetcher $dataFetcher,
        DatesFactory $datesFactory,
        CategoriesAssignment $categoriesAssignment,
        LocationAssignment $locationAssignment,
        Slugger $slugger
    ) {
        $this->eventRepository = $eventRepository;
        $this->organizerRepository = $organizerRepository;
        $this->dateRepository = $dateRepository;
        $this->metaDataRepository = $metaDataRepository;
        $this->configurationManager = $configurationManager;
        $this->persistenceManager = $persistenceManager;
        $this->objectManager = $objectManager;
        $this->dataFetcher = $dataFetcher;
        $this->datesFactory = $datesFactory;
        $this->categoriesAssignment = $categoriesAssignment;
        $this->locationAssignment = $locationAssignment;
        $this->slugger = $slugger;
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
        } catch (Exception $e) {
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

            $this->tmpCurrentEvent->setLocation(
                $this->locationAssignment->getLocation($event)
            );

            // Set Categories
            if ($event['categories'] ?? false) {
                $this->setCategories($event['categories']);
            }

            // Set Features
            if ($event['features']) {
                $this->setFeatures($event['features']);
            }

            // Set Organizer
            if ($event['addresses'] ?? false) {
                $this->setOrganizer($event['addresses']);
            }

            // Set Social
            if ($event['media_objects'] ?? false) {
                $this->setSocial($event['media_objects']);
            }

            if ($event['web'] ?? false) {
                $this->tmpCurrentEvent->setWeb($event['web']);
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

        $this->logger->info('Update slugs');
        $this->slugger->update('tx_events_domain_model_event');
        $this->slugger->update('tx_events_domain_model_date');

        $this->logger->info('Finished import');
        return 0;
    }

    private function setCategories(array $categories): void
    {
        $categories = $this->categoriesAssignment->getCategories(new CategoryImport(
            $this->import->getCategoryParent(),
            $this->import->getCategoriesPid(),
            $categories
        ));

        $this->tmpCurrentEvent->setCategories($categories);
    }

    private function setFeatures(array $features): void
    {
        $features = $this->categoriesAssignment->getCategories(new CategoryImport(
            $this->import->getFeaturesParent(),
            $this->import->getFeaturesPid(),
            $features,
            true
        ));

        $this->tmpCurrentEvent->setFeatures($features);
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

        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
        ];
        $importFolder = $this->import->getFilesFolder();

        $error = false;

        foreach ($assets as $media_object) {
            if (
                $media_object['rel'] == "default"
                && in_array($media_object['type'], $allowedMimeTypes)
            ) {
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

        try {
            $response = $this->dataFetcher->fetchImage($fileUrl);
        } catch (Exception $e) {
            $this->logger->error('Cannot load file ' . $fileUrl);
            return '';
        }

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

        return mb_substr($string, 0, $lenght - 3) . ' ???';
    }
}
