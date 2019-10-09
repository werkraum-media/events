<?php

namespace Wrm\Events\Service;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\Index\MetaDataRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Log\LogManager;

use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

use Wrm\Events\Domain\Repository\DateRepository;
use Wrm\Events\Domain\Repository\EventRepository;
use Wrm\Events\Domain\Repository\OrganizerRepository;
use Wrm\Events\Domain\Repository\RegionRepository;


class DestinationDataImportService {

    /**
     * @var
     */
    protected $restUrl;
    /**
     * @var
     */
    protected $restLicenseKey;
    /**
     * @var
     */
    protected $restType;
    /**
     * @var
     */
    protected $restLimit;
    /**
     * @var
     */
    protected $restTemplate;
    /**
     * @var
     */
    protected $restExperience;
    /**
     * @var
     */
    protected $storagePid;
    /**
     * @var
     */
    protected $categoriesPid;
    /**
     * @var
     */
    protected $regionUid;
    /**
     * @var
     */
    protected $categoryParentUid;
    /**
     * @var
     */
    protected $filesFolder;
    /**
     * @var
     */
    protected $storage;
    /**
     * @var array
     */
    protected $settings = [];
    /*
     * @var
     */
    protected $environment;
    /**
     * @var bool
     */
    protected $tmpCurrentEvent = FALSE;
    /**
     * @var
     */
    protected $logger;
    /**
     * @var EventRepository
     */
    protected $eventRepository;
    /**
     * @var RegionRepository
     */
    protected $regionRepository;
    /**
     * @var OrganizerRepository
     */
    protected $organizerRepository;
    /**
     * @var DateRepository
     */
    protected $dateRepository;
    /**
     * @var CategoryRepository
     */
    protected $sysCategoriesRepository;
    /**
     * @var FileRepository
     */
    protected $fileRepository;
    /**
     * @var MetaDataRepository
     */
    protected $metaDataRepository;
    /**
     * @var ConfigurationManager
     */
    protected $configurationManager;
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;
    /**
     * @var ResourceFactory
     */
    protected $resourceFactory;

    /**
     * ImportService constructor.
     * @param EventRepository $eventRepository
     * @param RegionRepository $regionRepository
     * @param OrganizerRepository $organizerRepository
     * @param DateRepository $dateRepository
     * @param CategoryRepository $sysCategoriesRepository
     * @param FileRepository $fileRepository
     * @param MetaDataRepository $metaDataRepository
     * @param ConfigurationManager $configurationManager
     * @param PersistenceManager $persistenceManager
     * @param ResourceFactory $resourceFactory
     * @param ObjectManager $objectManager
     * @param Environment $environment
     */
    public function __construct(
        EventRepository $eventRepository,
        RegionRepository $regionRepository,
        OrganizerRepository $organizerRepository,
        DateRepository $dateRepository,
        CategoryRepository $sysCategoriesRepository,
        FileRepository $fileRepository,
        MetaDataRepository $metaDataRepository,
        ConfigurationManager $configurationManager,
        PersistenceManager $persistenceManager,
        ResourceFactory $resourceFactory,
        ObjectManager $objectManager,
        Environment $environment
    ) {
        $this->eventRepository          = $eventRepository;
        $this->regionRepository         = $regionRepository;
        $this->organizerRepository      = $organizerRepository;
        $this->dateRepository           = $dateRepository;
        $this->sysCategoriesRepository  = $sysCategoriesRepository;
        $this->fileRepository           = $fileRepository;
        $this->metaDataRepository       = $metaDataRepository;
        $this->configurationManager     = $configurationManager;
        $this->persistenceManager       = $persistenceManager;
        $this->resourceFactory          = $resourceFactory;
        $this->objectManager            = $objectManager;
        $this->environment              = $environment;

        // Get Typoscript Settings
        $this->settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'Events',
            'Pi1'
        );

        // Set properties
        $this->restUrl          = $this->settings['destinationData']['restUrl'];
        $this->restLicenseKey   = $this->settings['destinationData']['license'];
        $this->restType         = $this->settings['destinationData']['restType'];
        $this->restLimit        = $this->settings['destinationData']['restLimit'];
        $this->restTemplate     = $this->settings['destinationData']['restTemplate'];
        $this->sysCategoriesPid = $this->settings['destinationData']['categoriesPid'];
    }

    /**
     * @param $restExperience
     * @param $storagePid
     * @param $regionUid
     * @param $categoryParentUid
     * @param $filesFolder
     */
    public function import($restExperience, $storagePid, $regionUid, $categoryParentUid, $filesFolder) {

        $this->restExperience = $restExperience;
        $this->storagePid = $storagePid;
        $this->regionUid = $regionUid;
        $this->categoryParentUid = $categoryParentUid;
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
        $restUrl = $this->restUrl . '?experience=' . $this->restExperience . '&licensekey=' . $this->restLicenseKey . '&type=' . $this->restType . '&limit=' . $this->restLimit . '&template=' . $this->restTemplate;
        $this->logger->info('Try to get data from ' . $restUrl);

        if ($jsonResponse = json_decode(file_get_contents($restUrl),true)) {
            $this->logger->info('Received data with ' . count($jsonResponse['items']) . ' items');
            return $this->processData($jsonResponse);
        } else {
            $this->logger->error('Could not receive data.');
            return 1;
        }

    }

    /**
     * @param $data
     * @return int
     */
    public function processData($data) {

        $this->logger->info('Processing json ' . count($data['items']));

        // Get selected region
        $selectedRegion = $this->regionRepository->findByUid($this->regionUid);

        foreach ($data['items'] as $event) {

            $this->logger->info('Processing event ' . substr($event['title'], 0, 20));

            // Event already exists? If not create one!
            $this->tmpCurrentEvent = $this->getOrCreateEvent($event['global_id'], $event['title']);

            // Set selected Region
            $this->tmpCurrentEvent->setRegion($selectedRegion);

            // Set Title
            $this->tmpCurrentEvent->setTitle(substr($event['title'], 0, 254));

            // Set Highlight (Is only set in rest if true)
            if($event['highlight'])
                $this->tmpCurrentEvent->setHighlight($event['highlight']);

            // Set Texts
            if($event['texts'])
                $this->setTexts($event['texts']);

            // Set address and geo data
            if($event['street'] && $event['city'] && $event['zip'] && $event['country'])
                $this->setAddress($event['street'], $event['city'], $event['zip'], $event['country']);

            // Set LatLng
            if($event['geo']['main']['latitude'] && $event['geo']['main']['longitude'])
                $this->setLatLng($event['geo']['main']['latitude'], $event['geo']['main']['longitude']);

            // Set Categories
            if($event['categories'])
                $this->setCategories($event['categories']);

            // Set Organizer
            if($event['addresses'])
                $this->setOrganizer($event['addresses']);

            // Set Dates
            if($event['timeIntervals'])
                $this->setDates($event['timeIntervals']);

            // Set Assets
            if($event['media_objects'])
                $this->setAssets($event['media_objects']);

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
    protected function setCategories(Array $categories) {
        $sysParentCategory = $this->sysCategoriesRepository->findByUid($this->categoryParentUid);
        foreach ($categories as $categoryTitle) {
            $tmpSysCategory = $this->sysCategoriesRepository->findOneByTitle($categoryTitle);
            if (!$tmpSysCategory) {
                $this->logger->info('Creating new category: ' . $categoryTitle);
                $tmpSysCategory = $this->objectManager->get(\TYPO3\CMS\Extbase\Domain\Model\Category::class);
                $tmpSysCategory->setTitle($categoryTitle);
                $tmpSysCategory->setParent($sysParentCategory);
                $tmpSysCategory->setPid($this->sysCategoriesPid);
                $this->sysCategoriesRepository->add($tmpSysCategory);
                $this->tmpCurrentEvent->addCategory($tmpSysCategory);
            } else {
                $this->tmpCurrentEvent->addCategory($tmpSysCategory);
            }
        }
    }

    /**
     * @param array $timeIntervals
     * @TODO: split into functions
     */
    protected function setDates(Array $timeIntervals) {

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

        $now = new \DateTime();
        $now = $now->getTimestamp();

        foreach ($timeIntervals as $date) {

            // Check if dates are given as interval or not
            if (empty($date['interval'])) {

                if (strtotime($date['start']) > $now) {
                    $this->logger->info('Setup single date');
                    $dateObj = $this->objectManager->get(\Wrm\Events\Domain\Model\Date::class);
                    $start = new \DateTime($date['start'], new \DateTimeZone($date['tz']));
                    $end = new \DateTime($date['end'], new \DateTimeZone($date['tz']));
                    $this->logger->info('Start transformed ' . $start->format('Y-m-d H:i'));
                    $this->logger->info('End transformed ' . $end->format('Y-m-d H:i'));
                    $dateObj->setStart($start);
                    $dateObj->setEnd($end);
                    $this->tmpCurrentEvent->addDate($dateObj);
                }

            } else {

                if ($date['freq'] == 'Daily' && empty($date['weekdays'])) {
                    $this->logger->info('Setup daily interval dates');
                    $this->logger->info('Start ' . $date['start']);
                    $this->logger->info('End ' . $date['repeatUntil']);
                    $start = new \DateTime($date['start'], new \DateTimeZone($date['tz']));
                    $until = new \DateTime($date['repeatUntil'], new \DateTimeZone($date['tz']));
                    for($i = strtotime($start->format('l'), $start->getTimestamp()); $i <= $until->getTimestamp(); $i = strtotime('+1 day', $i)) {
                        if ($i > $now) {
                            $eventStart = new \DateTime();
                            $eventStart->setTimestamp($i);
                            $eventStart->setTime($start->format('H'), $start->format('i'));
                            $eventEnd = new \DateTime();
                            $eventEnd->setTimestamp($i);
                            $eventEnd->setTime($until->format('H'), $until->format('i'));
                            $dateObj = $this->objectManager->get(\Wrm\Events\Domain\Model\Date::class);
                            $dateObj->setStart($eventStart);
                            $dateObj->setEnd($eventEnd);
                            $this->tmpCurrentEvent->addDate($dateObj);
                        }
                    }
                }

                else if ($date['freq'] == 'Weekly' && !empty($date['weekdays'])) {
                    foreach ($date['weekdays'] as $day) {
                        $this->logger->info('Setup weekly interval dates for ' . $day);
                        $this->logger->info('Start ' . $date['start']);
                        $this->logger->info('End ' . $date['repeatUntil']);
                        $start = new \DateTime($date['start'], new \DateTimeZone($date['tz']));
                        $until = new \DateTime($date['repeatUntil'], new \DateTimeZone($date['tz']));

                        for($i = strtotime($day, $start->getTimestamp()); $i <= $until->getTimestamp(); $i = strtotime('+1 week', $i)) {
                            if ($i > $now) {
                                $eventStart = new \DateTime();
                                $eventStart->setTimestamp($i);
                                $eventStart->setTime($start->format('H'), $start->format('i'));
                                $eventEnd = new \DateTime();
                                $eventEnd->setTimestamp($i);
                                $eventEnd->setTime($until->format('H'), $until->format('i'));
                                $dateObj = $this->objectManager->get(\Wrm\Events\Domain\Model\Date::class);
                                $dateObj->setStart($eventStart);
                                $dateObj->setEnd($eventEnd);
                                $this->tmpCurrentEvent->addDate($dateObj);
                            }
                        }
                    }
                }
            }
            $this->logger->info('Finished setup dates');
        }
    }

    /**
     * @param array $addresses
     */
    protected function setOrganizer(Array $addresses) {
        foreach ($addresses as $address)
        {
            if ($address['rel'] == "organizer") {
                $tmpOrganizer = $this->organizerRepository->findOneByName($address['name']);
                if ($tmpOrganizer) {
                    $this->tmpCurrentEvent->setOrganizer($tmpOrganizer);
                    continue;
                }
                $tmpOrganizer = $this->objectManager->get(\Wrm\Events\Domain\Model\Organizer::class);
                $tmpOrganizer->setName($address['name']);
                $tmpOrganizer->setCity($address['city']);
                $tmpOrganizer->setZip($address['zip']);
                $tmpOrganizer->setStreet($address['street']);
                $tmpOrganizer->setPhone($address['phone']);
                $tmpOrganizer->setWeb($address['web']);
                $tmpOrganizer->setEmail($address['email']);
                $tmpOrganizer->setDistrict($address['district']);
                $this->organizerRepository->add($tmpOrganizer);
                $this->tmpCurrentEvent->setOrganizer($tmpOrganizer);
            }
        }
    }

    /**
     * @param string $street
     * @param string $city
     * @param string $zip
     * @param string $country
     */
    protected function setAddress(String $street, String $city, String $zip, String $country) {
        $this->tmpCurrentEvent->setStreet($street);
        $this->tmpCurrentEvent->setCity($city);
        $this->tmpCurrentEvent->setZip($zip);
        $this->tmpCurrentEvent->setCountry($country);
    }

    /**
     * @param string $lat
     * @param string $lng
     */
    protected function setLatLng(String $lat, String $lng) {
        $this->tmpCurrentEvent->setLatitude($lat);
        $this->tmpCurrentEvent->setLongitude($lng);
    }

    /**
     * Set Texts
     * @param Array $texts
     */
    protected function setTexts(Array $texts) {
        foreach ($texts as $text)
        {
            if ($text['rel'] == "details" && $text['type'] == "text/plain") {
                $this->tmpCurrentEvent->setDetails($text['value']);
            }
            if ($text['rel'] == "teaser" && $text['type'] == "text/plain") {
                $this->tmpCurrentEvent->setTeaser($text['value']);
            }
        }
    }

    /**
     * Load File
     * @param String $globalId
     * @param String $title
     */
    protected function getOrCreateEvent(String $globalId, String $title) {

        $event = $this->eventRepository->findOneByGlobalId($globalId);

        if ($event) {
            // Global ID is found and events gets updated
            $event = $this->eventRepository->findOneByGlobalId($globalId);
            $this->logger->info('Found "' . substr($title, 0, 20) . '..." with global id ' . $globalId . ' in database');
            return $event;
        }

        // New event is created
        $this->logger->info(substr($title, 0, 20) . ' does not exist');
        $event = $this->objectManager->get(\Wrm\Events\Domain\Model\Event::class);
        // Create event and persist
        $event->setGlobalId($globalId);
        $event->setCategories(new ObjectStorage());
        $this->eventRepository->add($event);
        $this->persistenceManager->persistAll();
        $this->logger->info('Not found "' . substr($title, 0, 20)  . '..." with global id ' . $globalId . ' in database. Created new one.');
        return $event;
    }

    /**
     * @param array $assets
     */
    protected function setAssets(Array $assets) {

        $this->logger->info("Set assets");

        $error = false;

        foreach ($assets as $media_object)
        {
            if($media_object['rel'] == "default" && $media_object['type'] == "image/jpeg") {

                $this->storage = $this->resourceFactory->getDefaultStorage();

                if ($this->storage == null) {
                    $this->logger->error('No default storage defined. Cancel import.');
                    die();
                }

                // Check if file already exists
                if (file_exists($this->environment->getPublicPath() . '/fileadmin/' . $this->filesFolder . strtolower(basename($media_object['url'])))) {
                    $this->logger->info('File already exists');
                } else {
                    $this->logger->info("File don't exist");
                    // Load the file
                    if ($file = $this->loadFile($media_object['url'])) {
                        // Move file to defined folder
                        $this->logger->info('Adding file ' . $file);
                        $this->storage->addFile($this->environment->getPublicPath() . "/uploads/tx_events/" . $file, $this->storage->getFolder($this->filesFolder), basename($media_object['url']));
                    } else {
                        $error = true;
                    }
                }

                if ($error !== true) {
                    if ($this->tmpCurrentEvent->getImages() !== null) {
                        $this->logger->info('Relation found');
                        // TODO: How to delete file references?
                    } else {
                        $this->logger->info('No relation found');
                        $file = $this->storage->getFile($this->filesFolder . basename($media_object['url']));
                        $this->metaDataRepository->update($file->getUid(), array('title' => $media_object['value'], 'description' => $media_object['description'], 'alternative' => 'DD Import'));
                        $this->createFileRelations($file->getUid(),  'tx_events_domain_model_event', $this->tmpCurrentEvent->getUid(), 'images', $this->storagePid);
                    }
                }

            }
            $error = false;
        }
    }

    /**
     * Load File
     * @param string $file
     * @return string
     */
    protected function loadFile($file) {
        $directory = $this->environment->getPublicPath() . "/uploads/tx_events/";
        $filename = basename($file);
        $this->logger->info('Getting file ' . $file . ' as ' . $filename);
        $asset = GeneralUtility::getUrl($file);
        if ($asset) {
            file_put_contents($directory . $filename, $asset);
            return $filename;
        }
        $this->logger->error('Cannot load file ' . $file);
        return false;
    }

    /**
     * Build relations for FAL
     * @param int    $uid_local
     * @param string $tablenames
     * @param int    $uid_foreign
     * @param string $fieldname
     * @param string $storagePid
     * @return bool
     */
    protected function createFileRelations($uid_local, $tablenames, $uid_foreign, $fieldname, $storagePid) {

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

        $dataHandler = $this->objectManager->get(\TYPO3\CMS\Core\DataHandling\DataHandler::class);
        $dataHandler->start($data, array());
        $dataHandler->process_datamap();

        if (count($dataHandler->errorLog) === 0) {
            return true;
        }

        foreach($dataHandler->errorLog as $error) {
            $this->logger->info($error);
        }
        return false;
    }

    /**
     * Performs slug update
     * @return bool
     */
    protected function doSlugUpdate()
    {
        $this->logger->info('Update slugs');

        $slugHelper = GeneralUtility::makeInstance(
            SlugHelper::class,
            'tx_events_domain_model_event',
            'slug',
            $GLOBALS['TCA']['tx_events_domain_model_event']['columns']['slug']['config']
        );

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_events_domain_model_event');
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

        return true;
    }
}
