<?php

namespace Wrm\Events\Service;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Resource\Exception\FolderDoesNotExistException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Index\MetaDataRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
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
use Wrm\Events\Domain\Model\Organizer;
use Wrm\Events\Domain\Model\Region;
use Wrm\Events\Domain\Repository\CategoryRepository;
use Wrm\Events\Domain\Repository\DateRepository;
use Wrm\Events\Domain\Repository\EventRepository;
use Wrm\Events\Domain\Repository\OrganizerRepository;
use Wrm\Events\Domain\Repository\RegionRepository;
use Wrm\Events\Service\DestinationDataImportService\DataFetcher;

class DestinationDataImportService
{
    /**
     * @var string
     */
    private $restUrl;

    /**
     * @var string
     */
    private $restLicenseKey;

    /**
     * @var string
     */
    private $restType;

    /**
     * @var string
     */
    private $restLimit;

    /**
     * @var string
     */
    private $restMode;

    /**
     * @var string
     */
    private $restTemplate;

    /**
     * @var string
     */
    private $restExperience;

    /**
     * @var int
     */
    private $storagePid;

    /**
     * @var ?int
     */
    private $regionUid;

    /**
     * @var int
     */
    private $categoriesPid;

    /**
     * @var int
     */
    private $categoryParentUid;

    /**
     * @var string
     */
    private $filesFolder;

    /**
     * @var array
     */
    private $settings = [];

    /**
     * @var Environment
     */
    private $environment;

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
     * @var RegionRepository
     */
    private $regionRepository;

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
     * @var ResourceFactory
     */
    private $resourceFactory;

    /**
     * @var DataFetcher
     */
    private $dataFetcher;

    /**
     * ImportService constructor.
     * @param EventRepository $eventRepository
     * @param RegionRepository $regionRepository
     * @param OrganizerRepository $organizerRepository
     * @param DateRepository $dateRepository
     * @param CategoryRepository $sysCategoriesRepository
     * @param MetaDataRepository $metaDataRepository
     * @param ConfigurationManager $configurationManager
     * @param PersistenceManager $persistenceManager
     * @param ResourceFactory $resourceFactory
     * @param ObjectManager $objectManager
     * @param Environment $environment
     * @param DataFetcher $dataFetcher
     */
    public function __construct(
        EventRepository $eventRepository,
        RegionRepository $regionRepository,
        OrganizerRepository $organizerRepository,
        DateRepository $dateRepository,
        CategoryRepository $sysCategoriesRepository,
        MetaDataRepository $metaDataRepository,
        ConfigurationManager $configurationManager,
        PersistenceManager $persistenceManager,
        ResourceFactory $resourceFactory,
        ObjectManager $objectManager,
        Environment $environment,
        DataFetcher $dataFetcher
    ) {
        $this->eventRepository = $eventRepository;
        $this->regionRepository = $regionRepository;
        $this->organizerRepository = $organizerRepository;
        $this->dateRepository = $dateRepository;
        $this->sysCategoriesRepository = $sysCategoriesRepository;
        $this->metaDataRepository = $metaDataRepository;
        $this->configurationManager = $configurationManager;
        $this->persistenceManager = $persistenceManager;
        $this->resourceFactory = $resourceFactory;
        $this->objectManager = $objectManager;
        $this->environment = $environment;
        $this->dataFetcher = $dataFetcher;

        // Get Typoscript Settings
        $this->settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'Events',
            'Pi1'
        );

        // Set properties
        $this->restUrl = $this->settings['destinationData']['restUrl'];
        $this->restLicenseKey = $this->settings['destinationData']['license'];
        $this->restType = $this->settings['destinationData']['restType'];
        $this->restLimit = $this->settings['destinationData']['restLimit'];
        $this->restMode = $this->settings['destinationData']['restMode'];
        $this->restTemplate = $this->settings['destinationData']['restTemplate'];
        $this->categoriesPid = (int) $this->settings['destinationData']['categoriesPid'];
        $this->categoryParentUid = (int) $this->settings['destinationData']['categoryParentUid'];
    }

    public function import(
        string $restExperience,
        int $storagePid,
        ?int $regionUid,
        string $filesFolder
    ): int {
        $this->restExperience = $restExperience;
        $this->storagePid = $storagePid;
        $this->regionUid = $regionUid;
        $this->filesFolder = $filesFolder;

        // Get configuration
        $frameworkConfiguration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );

        // Set storage pid
        $persistenceConfiguration = [
            'persistence' => [
                'storagePid' => $this->storagePid,
            ],
        ];

        // Set Configuration
        $this->configurationManager->setConfiguration(array_merge($frameworkConfiguration, $persistenceConfiguration));
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);

        $this->logger->info('Starting Destination Data Import Service');
        $restUrl = $this->restUrl . '?experience=' . $this->restExperience . '&licensekey=' . $this->restLicenseKey . '&type=' . $this->restType . '&mode=' . $this->restMode . '&limit=' . $this->restLimit . '&template=' . $this->restTemplate;
        $this->logger->info('Try to get data from ' . $restUrl);

        try {
            $fetchedData = $this->fetchData($restUrl);
        } catch (\Exception $e) {
            $this->logger->error('Could not receive data.');
            return 1;
        }

        return $this->processData($fetchedData);
    }

    private function fetchData(string $restUrl): array
    {
        $jsonContent = file_get_contents($restUrl);
        if (is_string($jsonContent) === false) {
            throw new \Exception('Could not receive data.', 1639495835);
        }
        $jsonResponse = json_decode($jsonContent, true);
        if (is_array($jsonResponse) === false) {
            throw new \Exception('Could not receive data.', 1639495835);
        }

        $this->logger->info('Received data with ' . count($jsonResponse['items']) . ' items');
        return $jsonResponse;
    }

    public function processData(array $data): int
    {
        $this->logger->info('Processing json ' . count($data['items']));

        // Get selected region
        $selectedRegion = null;
        if (is_int($this->regionUid)) {
            $selectedRegion = $this->regionRepository->findByUid($this->regionUid);
        }

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
            if ($event['highlight']) {
                $this->tmpCurrentEvent->setHighlight($event['highlight']);
            }

            // Set Texts
            if ($event['texts']) {
                $this->setTexts($event['texts']);
            }

            // Set address and geo data
            if ($event['name'] || $event['street'] || $event['city'] || $event['zip'] || $event['country'] || $event['web']) {
                $this->setAddress($event);
            }

            // Set LatLng
            if ($event['geo']['main']['latitude'] && $event['geo']['main']['longitude']) {
                $this->setLatLng($event['geo']['main']['latitude'], $event['geo']['main']['longitude']);
            }

            // Set Categories
            if ($event['categories']) {
                $this->setCategories($event['categories']);
            }

            // Set Organizer
            if ($event['addresses']) {
                $this->setOrganizer($event['addresses']);
            }

            // Set Social
            if ($event['media_objects']) {
                $this->setSocial($event['media_objects']);
            }

            // Set Tickets
            if ($event['media_objects']) {
                $this->setTickets($event['media_objects']);
            }

            // Set Dates
            if ($event['timeIntervals']) {
                $this->setDates(
                    $event['timeIntervals'],
                    (bool) $this->getAttributeValue($event, 'DETAILS_ABGESAGT')
                );
            }

            // Set Assets
            if ($event['media_objects']) {
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
        $sysParentCategory = $this->sysCategoriesRepository->findByUid($this->categoryParentUid);
        if (!$sysParentCategory instanceof Category) {
            $this->logger->warning(
                'Could not fetch system parent category by uid.',
                ['uid' => $this->categoryParentUid]
            );
            return;
        }

        foreach ($categories as $categoryTitle) {
            $tmpSysCategory = $this->sysCategoriesRepository->findOneByTitle($categoryTitle);
            if (!$tmpSysCategory) {
                $this->logger->info('Creating new category: ' . $categoryTitle);
                $tmpSysCategory = $this->objectManager->get(Category::class);
                $tmpSysCategory->setTitle($categoryTitle);
                $tmpSysCategory->setParent($sysParentCategory);
                $tmpSysCategory->setPid($this->categoriesPid);
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

        $today = new \DateTime('today');
        $today = $today->getTimestamp();

        foreach ($timeIntervals as $date) {
            // Check if dates are given as interval or not
            if (empty($date['interval'])) {
                if (strtotime($date['start']) > $today) {
                    $this->logger->info('Setup single date');
                    $start = new \DateTime($date['start'], new \DateTimeZone($date['tz']));
                    $end = new \DateTime($date['end'], new \DateTimeZone($date['tz']));
                    $this->logger->info('Start transformed ' . $start->format('Y-m-d H:i'));
                    $this->logger->info('End transformed ' . $end->format('Y-m-d H:i'));
                    $this->tmpCurrentEvent->addDate(Date::createFromDestinationData(
                        $start,
                        $end,
                        $canceled
                    ));
                }
            } else {
                if ($date['freq'] == 'Daily' && empty($date['weekdays']) && !empty($date['repeatUntil'])) {
                    $this->logger->info('Setup daily interval dates');
                    $this->logger->info('Start ' . $date['start']);
                    $this->logger->info('End ' . $date['repeatUntil']);
                    $start = new \DateTime($date['start'], new \DateTimeZone($date['tz']));
                    $until = new \DateTime($date['repeatUntil'], new \DateTimeZone($date['tz']));

                    $i = (int) strtotime($start->format('l'), $start->getTimestamp());
                    while ($i !== 0 && $i <= $until->getTimestamp()) {
                        $i = (int) strtotime('+1 day', $i);

                        if ($i >= $today) {
                            $eventStart = new \DateTime();
                            $eventStart->setTimestamp($i);
                            $eventStart->setTime((int) $start->format('H'), (int) $start->format('i'));
                            $eventEnd = new \DateTime();
                            $eventEnd->setTimestamp($i);
                            $eventEnd->setTime((int) $until->format('H'), (int) $until->format('i'));
                            $this->tmpCurrentEvent->addDate(Date::createFromDestinationData(
                                $eventStart,
                                $eventEnd,
                                $canceled
                            ));
                        }
                    }
                } elseif ($date['freq'] == 'Weekly' && !empty($date['weekdays']) && !empty($date['repeatUntil'])) {
                    foreach ($date['weekdays'] as $day) {
                        $this->logger->info('Setup weekly interval dates for ' . $day);
                        $this->logger->info('Start ' . $date['start']);
                        $this->logger->info('End ' . $date['repeatUntil']);
                        $start = new \DateTime($date['start'], new \DateTimeZone($date['tz']));
                        $until = new \DateTime($date['repeatUntil'], new \DateTimeZone($date['tz']));

                        for ($i = strtotime($day, $start->getTimestamp()); $i <= $until->getTimestamp(); $i = strtotime('+1 week', $i)) {
                            if ($i >= $today) {
                                $eventStart = new \DateTime();
                                $eventStart->setTimestamp($i);
                                $eventStart->setTime((int) $start->format('H'), (int) $start->format('i'));
                                $eventEnd = new \DateTime();
                                $eventEnd->setTimestamp($i);
                                $eventEnd->setTime((int) $until->format('H'), (int) $until->format('i'));
                                $this->tmpCurrentEvent->addDate(Date::createFromDestinationData(
                                    $eventStart,
                                    $eventEnd,
                                    $canceled
                                ));
                            }
                        }
                    }
                }
            }
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

    /**
     * @param array $media
     */
    private function setTickets(array $media): void
    {
        foreach ($media as $link) {
            if ($link['rel'] == "ticket") {
                $this->tmpCurrentEvent->setTicket($link['url']);
                break;
            } elseif ($link['rel'] == "booking" && !$this->multiArrayKeyExists('ticket', $media)) {
                $this->tmpCurrentEvent->setTicket($link['url']);
                break;
            } elseif ($link['rel'] == "PRICE_KARTENLINK" && !$this->multiArrayKeyExists('ticket', $media) && !$this->multiArrayKeyExists('booking', $media)) {
                $this->tmpCurrentEvent->setTicket($link['url']);
            }
        }
    }

    private function multiArrayKeyExists(string $needle, array $haystack): bool
    {
        foreach ($haystack as $key => $value) {
            if ($needle == $key) {
                return true;
            }
            if (is_array($value)) {
                if ($this->multiArrayKeyExists($needle, $value) == true) {
                    return true;
                }
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

        $storage = $this->resourceFactory->getDefaultStorage();
        if (!$storage instanceof ResourceStorage) {
            $this->logger->error('No default storage defined. Cancel import.');
            exit();
        }

        $error = false;

        foreach ($assets as $media_object) {
            if ($media_object['rel'] == "default" && $media_object['type'] == "image/jpeg") {
                $orgFileUrl = urldecode($media_object['url']);
                $orgFileNameSanitized = $storage->sanitizeFileName(
                    basename(
                        urldecode($media_object['url'])
                    )
                );

                $this->logger->info('File attached:' . $orgFileUrl);
                $this->logger->info('File attached sanitized:' . $orgFileNameSanitized);

                $targetFilePath = $this->environment->getPublicPath() . '/fileadmin/' . $this->filesFolder
                    . $orgFileNameSanitized;
                // Check if file already exists

                if (file_exists($targetFilePath)) {
                    $this->logger->info('File already exists');
                } else {
                    $this->logger->info("File don't exist " . $orgFileNameSanitized);
                    // Load the file
                    if ($file = $this->loadFile($orgFileUrl)) {
                        // Move file to defined folder
                        $this->logger->info('Adding file ' . $file);

                        try {
                            $targetFolder = $storage->getFolder($this->filesFolder);
                        } catch (FolderDoesNotExistException $e) {
                            $targetFolder = $storage->createFolder($this->filesFolder);
                        }

                        $tempFilePath = $this->environment->getPublicPath() . "/uploads/tx_events/" . $file;
                        $storage->addFile($tempFilePath, $targetFolder);
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
                        $fileIdentifier = $this->filesFolder . $orgFileNameSanitized;
                        $file = $storage->getFile($fileIdentifier);
                        if (!$file instanceof File) {
                            $this->logger->warning('Could not find file.', [$fileIdentifier]);
                            continue;
                        }
                        $this->metaDataRepository->update(
                            $file->getUid(),
                            [
                                'title' => $media_object['value'],
                                'description' => $media_object['description'],
                                'alternative' => 'DD Import'
                            ]
                        );
                        $this->createFileRelations(
                            $file->getUid(),
                            'tx_events_domain_model_event',
                            $this->tmpCurrentEvent->getUid(),
                            'images',
                            $this->storagePid
                        );
                    }
                }
            }
            $error = false;
        }
    }

    private function loadFile(string $fileUrl): string
    {
        $directory = $this->environment->getPublicPath() . "/uploads/tx_events/";
        $filename = basename($fileUrl);
        $this->logger->info('Getting file ' . $fileUrl . ' as ' . $filename);

        $response = $this->dataFetcher->fetchImage($fileUrl);
        $asset = $response->getBody()->__toString();
        if ($response->getStatusCode() === 200 && $asset !== '') {
            file_put_contents($directory . $filename, $asset);
            return $filename;
        }

        $this->logger->error('Cannot load file ' . $fileUrl);
        return '';
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
}
