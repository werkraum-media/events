<?php

namespace Wrm\Events\Service;

use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\Index\MetaDataRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
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

class CleanupService {

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
        ObjectManager $objectManager
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
        $this->restTemplate     = $this->settings['destinationData']['dataTemplate'];

        // Init Logger
        // Nötig, damit logger arbeitet?
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['writerConfiguration'] = [
            \TYPO3\CMS\Core\Log\LogLevel::INFO => [
                'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => [
                    'logFile' => 'typo3temp/logs/events_cleanup'
                    ]
            ]
        ];

        $this->logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Log\LogManager::class)->getLogger(__CLASS__);
        $this->logger->info('Starting Destination Data Import Service');
    }

    /**
     * @param $restExperience
     * @param $storagePid
     * @param $regionUid
     * @param $categoryParentUid
     * @param $filesFolder
     */
    public function doCleanup() {
        

    }

    /**
     * @param $data
     * @return int
     */
    public function processData($data) {

    }
    
}