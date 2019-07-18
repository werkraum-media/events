<?php
namespace Wrm\Events\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Core\Resource\Index\MetaDataRepository;
use TYPO3\CMS\Core\Core\Bootstrap;

class ImportCommand extends Command {

    protected $restUrl;
    protected $restExperience;
    protected $restLicenseKey;
    protected $restType;
    protected $restLimit;
    protected $restTemplate;

    protected $storagePid;
    protected $regionUid;
    protected $categoryParentUid;
    protected $logger;
    protected $filesFolder;

    protected $cliOutput;
    protected $cliInput;

    protected $eventRepository;
    protected $regionRepository;
    protected $organizerRepository;
    protected $dateRepository;
    protected $sysCategoriesRepository;
    protected $fileRepository;
    protected $metaDataRepository;

    protected $configurationManager;
    protected $objectManager;
    protected $persistenceManager;
    protected $resourceFactory;

    protected $storage;

    protected $tmpCurrentEvent = FALSE;

    public function configure()
    {
        $this->setDescription('Import Destination Data Events');
        $this->setHelp('Destination Data Events are imported');

        $this->addArgument(
            'storage-pid',
            InputArgument::OPTIONAL,
            'What is the storage pid?',
            '281'
        );
        $this->addArgument(
            'region-uid',
            InputArgument::OPTIONAL,
            'What is the region uid?',
            '3'
        );
        $this->addArgument(
            'category-parent-uid',
            InputArgument::OPTIONAL,
            'What is the default category parent uid?',
            '6'
        );
        $this->addArgument('rest-url',
            InputArgument::OPTIONAL,
            'What is the rest url?',
            'http://meta.et4.de/rest.ashx/search/'
        );
        $this->addArgument('rest-experience',
            InputArgument::OPTIONAL,
            'What is the rest experience?',
            'arnstadt'
        );
        $this->addArgument('rest-license-key',
            InputArgument::OPTIONAL,
            'What is the rest license key?',
            '***REMOVED***'
        );
        $this->addArgument('rest-type',
            InputArgument::OPTIONAL,
            'What is the rest data type?',
            'Event'
        );
        $this->addArgument('rest-limit',
            InputArgument::OPTIONAL,
            'What is the rest data limit?',
            '200'
        );
        $this->addArgument('rest-template',
            InputArgument::OPTIONAL,
            'What is the rest data template?',
            'ET2014A.json'
        );
        $this->addArgument('files-folder',
            InputArgument::OPTIONAL,
            'Where to save the image files?',
            'redaktion/arnstadt/events/'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cliOutput = $output;
        $this->cliInput = $input;

        $this->storagePid = $input->getArgument('storage-pid');
        $this->regionUid = $input->getArgument('region-uid');
        $this->categoryParentUid = $input->getArgument('category-parent-uid');
        $this->filesFolder = $input->getArgument('files-folder');

        $this->restUrl = $input->getArgument('rest-url');
        $this->restExperience = $input->getArgument('rest-experience');
        $this->restLicenseKey = $input->getArgument('rest-license-key');
        $this->restType = $input->getArgument('rest-type');
        $this->restLimit = $input->getArgument('rest-limit');
        $this->restTemplate = $input->getArgument('rest-template');

        $this->cliOutput->writeln('Hello from destination data event import');

        Bootstrap::initializeBackendAuthentication();

        return $this->import();
    }

    protected function import() {

        try {
            // Debug limit to one select event via query
            // $restUrl = $this->restUrl . '?experience=' . $this->restExperience . '&licensekey=' . $this->restLicenseKey . '&type=' . $this->restType . '&limit=' . $this->restLimit . '&template=' . $this->restTemplate . "&q=title:Schüler";

            $restUrl = $this->restUrl . '?experience=' . $this->restExperience . '&licensekey=' . $this->restLicenseKey . '&type=' . $this->restType . '&limit=' . $this->restLimit . '&template=' . $this->restTemplate;
            $this->cliOutput->writeln($restUrl);
            $jsonResponse = json_decode(file_get_contents($restUrl),true);

        } catch (\Exception $e) {
            return 1;
        }

        if (!empty($jsonResponse)) {

            $this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
            $this->configurationManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
            $this->persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');

            $this->eventRepository = $this->objectManager->get('Wrm\\Events\\Domain\\Repository\\EventRepository');
            $this->regionRepository = $this->objectManager->get('Wrm\\Events\\Domain\\Repository\\RegionRepository');
            $this->organizerRepository = $this->objectManager->get('Wrm\\Events\\Domain\\Repository\\OrganizerRepository');
            $this->dateRepository = $this->objectManager->get('Wrm\\Events\\Domain\\Repository\\DateRepository');

            $this->sysCategoriesRepository = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Domain\\Repository\\CategoryRepository');

            $frameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
            $persistenceConfiguration = [
                'persistence' => [
                    'storagePid' => $this->storagePid,
                ],
            ];
            $this->configurationManager->setConfiguration(array_merge($frameworkConfiguration, $persistenceConfiguration));

            $this->cliOutput->writeln('Received json data.');
            $this->cliOutput->writeln('Received ' . $jsonResponse['count'] . ' items.');

            // get selected Region from Database
            $selectedRegion = $this->regionRepository->findByUid($this->regionUid);

            // TODO: How to delete event Dates
            // until than delet all dates -->

            // $this->dateRepository->removeByEvent();

            // <--

            foreach ($jsonResponse['items'] as $event)
            {

                $this->tmpCurrentEvent = $this->eventRepository->findOneByGlobalId($event['global_id']);

                if (!$this->tmpCurrentEvent)
                {
                    // Global ID not found
                    $this->tmpCurrentEvent = $this->objectManager->get('Wrm\\Events\\Domain\\Model\\Event');
                    //$this->currentEventIsNew = TRUE;

                    // Create event and persist
                    $this->tmpCurrentEvent->setGlobalId($event['global_id']);
                    $this->tmpCurrentEvent->setCategories(new ObjectStorage());
                    $this->eventRepository->add($this->tmpCurrentEvent);
                    $this->persistenceManager->persistAll();

                    $this->cliOutput->writeln('Not found "' . substr($event['title'], 0, 20)  . '..." with global id ' . $event['global_id'] . ' in database. Created new one.');
                } else {
                    // Global ID is found and events gets updated
                    $this->tmpCurrentEvent = $this->eventRepository->findOneByGlobalId($event['global_id']);
                    //$this->currentEventIsNew = FALSE;
                    $this->cliOutput->writeln('Found "' . substr($event['title'], 0, 20) . '..." with global id ' . $event['global_id'] . ' in database');
                }

                // Set selected Region
                $this->tmpCurrentEvent->setRegion($selectedRegion);
                // Set Title
                $this->tmpCurrentEvent->setTitle(substr($event['title'], 0, 254));
                // Set Highlight (Is only set in rest if true)
                if($event['highlight'])
                    $this->tmpCurrentEvent->setHighlight($event['highlight']);

                // Set Texts
                foreach ($event['texts'] as $text)
                {
                    if ($text['rel'] == "details" && $text['type'] == "text/plain") {
                        $this->tmpCurrentEvent->setDetails($text['value']);
                    }
                    if ($text['rel'] == "teaser" && $text['type'] == "text/plain") {
                        $this->tmpCurrentEvent->setTeaser($text['value']);
                    }
                }

                //////////////
                // Set Address
                $this->tmpCurrentEvent->setStreet($event['street']);
                $this->tmpCurrentEvent->setCity($event['city']);
                $this->tmpCurrentEvent->setZip($event['zip']);
                $this->tmpCurrentEvent->setCountry($event['country']);

                //////////
                // Set Geo
                $this->tmpCurrentEvent->setLatitude($event['geo']['main']['latitude']);
                $this->tmpCurrentEvent->setLongitude($event['geo']['main']['longitude']);

                /////////////////
                // Set Categories
                $tmpSysCategory = FALSE;
                $sysParentCategory = $this->sysCategoriesRepository->findByUid($this->categoryParentUid);

                foreach ($event['categories'] as $categoryTitle) {

                    $tmpSysCategory = $this->sysCategoriesRepository->findOneByTitle($categoryTitle);

                    if (!$tmpSysCategory)
                    {
                        $this->cliOutput->writeln('Creating new category: ' . $categoryTitle);
                        $tmpSysCategory = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Domain\\Model\\Category');
                        $tmpSysCategory->setTitle($categoryTitle);
                        $tmpSysCategory->setParent($sysParentCategory);
                        $this->sysCategoriesRepository->add($tmpSysCategory);
                        $this->tmpCurrentEvent->addCategory($tmpSysCategory);
                    } else {

                        $this->tmpCurrentEvent->addCategory($tmpSysCategory);
                    }

                    $tmpSysCategory = FALSE;
                }

                ////////////////
                // Set Organizer

                $tmpOrganizer = FALSE;
                foreach ($event['addresses'] as $address)
                {
                    if ($address['rel'] == "organizer") {
                        $tmpOrganizer = $this->organizerRepository->findOneByName($address['name']);
                        if (!$tmpOrganizer)
                        {
                            $tmpOrganizer = $this->objectManager->get('Wrm\\Events\\Domain\\Model\\Organizer');

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

                        } else {
                            $this->tmpCurrentEvent->setOrganizer($tmpOrganizer);
                        }
                        $tmpOrganizer = FALSE;
                    }
                }

                ////////////
                // Set Dates

                // TODO: does not seem to work -->
                //$currentEventDates = $this->tmpCurrentEvent->getDates();
                //$this->tmpCurrentEvent->removeAllDates($currentEventDates);
                // <--

                // TODO: Workaround delete dates
                $currentEventDates = $this->tmpCurrentEvent->getDates();
                $this->cliOutput->writeln('Found ' . count($currentEventDates) . ' to delete');

                foreach ($currentEventDates as $currentDate) {
                    //$this->cliOutput->writeln('Delete ' . $currentDate->getStart()->format('Y-m-d'));
                    $this->dateRepository->remove($currentDate);
                }

                $now = new \DateTime();
                $now = $now->getTimestamp();

                foreach ($event['timeIntervals'] as $date) {

                    // Check if dates are given as interval or not
                    if (empty($date['interval'])) {

                        if (strtotime($date['start']) > $now) {

                            $this->cliOutput->writeln('Setup single date');
                            //$this->cliOutput->writeln('Start ' . $date['start']);
                            //$this->cliOutput->writeln('End ' . $date['end']);

                            $dateObj = $this->objectManager->get('Wrm\\Events\\Domain\\Model\\Date');

                            $start = new \DateTime($date['start'], new \DateTimeZone($date['tz']));
                            $end = new \DateTime($date['end'], new \DateTimeZone($date['tz']));

                            $this->cliOutput->writeln('Start transformed ' . $start->format('Y-m-d H:i'));
                            $this->cliOutput->writeln('End transformed ' . $end->format('Y-m-d H:i'));

                            $dateObj->setStart($start);
                            $dateObj->setEnd($end);

                            $this->tmpCurrentEvent->addDate($dateObj);

                        }

                    } else {


                        if ($date['freq'] == 'Daily' && empty($date['weekdays'])) {

                            $this->cliOutput->writeln('Setup daily interval dates');
                            $this->cliOutput->writeln('Start ' . $date['start']);
                            $this->cliOutput->writeln('End ' . $date['repeatUntil']);

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

                                    //$this->cliOutput->writeln($eventStart->format('Y-m-d H:i'));
                                    //$this->cliOutput->writeln($eventEnd->format('Y-m-d H:i'));

                                    $dateObj = $this->objectManager->get('Wrm\\Events\\Domain\\Model\\Date');
                                    $dateObj->setStart($eventStart);
                                    $dateObj->setEnd($eventEnd);
                                    $this->tmpCurrentEvent->addDate($dateObj);

                                }

                            }

                        }

                        else if ($date['freq'] == 'Weekly' && !empty($date['weekdays'])) {

                            foreach ($date['weekdays'] as $day) {

                                $this->cliOutput->writeln('Setup weekly interval dates for ' . $day);
                                $this->cliOutput->writeln('Start ' . $date['start']);
                                $this->cliOutput->writeln('End ' . $date['repeatUntil']);

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

                                        //$this->cliOutput->writeln($eventStart->format('Y-m-d H:i'));
                                        //$this->cliOutput->writeln($eventEnd->format('Y-m-d H:i'));

                                        $dateObj = $this->objectManager->get('Wrm\\Events\\Domain\\Model\\Date');
                                        $dateObj->setStart($eventStart);
                                        $dateObj->setEnd($eventEnd);
                                        $this->tmpCurrentEvent->addDate($dateObj);

                                    }

                                }
                            }

                        }

                    }

                }

                /////////////
                // Set Assets

                $this->resourceFactory = $this->objectManager->get('TYPO3\\CMS\\Core\\Resource\\ResourceFactory');
                $this->fileRepository = $this->objectManager->get('TYPO3\\CMS\\Core\\Resource\\FileRepository');
                $this->metaDataRepository = $this->objectManager->get('TYPO3\\CMS\\Core\\Resource\\Index\\MetaDataRepository');

                foreach ($event['media_objects'] as $media_object)
                {
                    if($media_object['rel'] == "default" && $media_object['type'] == "image/jpeg") {

                        //

                        $this->storage = $this->resourceFactory->getDefaultStorage();

                        // Check if file already exists
                        if (file_exists(PATH_site . '/fileadmin/' . $this->filesFolder . strtolower(basename($media_object['url'])))) {
                            $this->cliOutput->writeln('[NOTICE] File already exists');
                        } else {
                            $this->cliOutput->writeln("[NOTICE] File don't exist");
                            // Load the file
                            if ($file = $this->loadFile($media_object['url'])) {
                                // Move file to defined folder
                                $this->cliOutput->writeln('[INFO] Adding file ' . $file);
                                $this->storage->addFile(PATH_site . "uploads/tx_Events/" . $file, $this->storage->getFolder($this->filesFolder), basename($media_object['url']));
                            } else {
                                $error = true;
                            }
                        }

                        if ($error !== true) {
                            if ($this->tmpCurrentEvent->getImages() !== null) {
                                $this->cliOutput->writeln('Relation found');
                                // TODO: How to delete file references?
                            } else {
                                $this->cliOutput->writeln('No relation found');
                                $file = $this->storage->getFile($this->filesFolder . basename($media_object['url']));
                                $this->metaDataRepository->update($file->getUid(), array('title' => $media_object['value'], 'description' => $media_object['description'], 'alternative' => 'DD Import'));
                                $this->createFileRelations($file->getUid(),  'tx_Events_domain_model_event', $this->tmpCurrentEvent->getUid(), 'images', $this->storagePid);
                            }
                        }

                    }
                }

                // Update and persist
                $this->cliOutput->writeln('Persist database');
                $this->eventRepository->update($this->tmpCurrentEvent);
                $this->persistenceManager->persistAll();

            }
        }

        $this->doSlugUpdate();

        return 0;
    }

    /**
     * Load File
     *
     * @param string $asset
     * @return bool
     */
    protected function loadFile($file) {
        $directory = PATH_site . "uploads/tx_Events/";
        $filename = basename($file);
        $this->cliOutput->writeln('[INFO] Getting file ' . $file . ' as ' . $filename);
        $asset = \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl($file);

        if ($asset) {
            $fp = fopen($directory . $filename, 'w');
            fputs($fp, $asset);
            fclose($fp);

            return $filename;
        }
        $this->cliOutput->writeln('[ERROR] cannot load file ' . $file);
        return false;
    }

    /**
     * Build relations for FAL
     *
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

        $dataHandler = $this->objectManager->get('TYPO3\\CMS\\Core\\DataHandling\\DataHandler');
        $dataHandler->start($data, array());
        $dataHandler->process_datamap();

        if (count($dataHandler->errorLog) === 0) {
            return true;
        } else {
            foreach($dataHandler->errorLog as $error) {
                $this->cliOutput->writeln($error);
            }
            return false;
        }
    }

    /*
     * Generate random hash for filenames
     */
    protected function randHash($len=32)
    {
        return substr(md5(openssl_random_pseudo_bytes(20)),-$len);
    }

    /**
     * Performs slug update
     *
     * @return bool
     */
    protected function doSlugUpdate()
    {

        $this->cliOutput->writeln('Update slugs');

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