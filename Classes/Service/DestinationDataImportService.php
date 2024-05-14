<?php

namespace Wrm\Events\Service;

use Exception;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use Wrm\Events\Caching\CacheManager;
use Wrm\Events\Domain\Model\Event;
use Wrm\Events\Domain\Model\Import;
use Wrm\Events\Domain\Model\Organizer;
use Wrm\Events\Domain\Model\Region;
use Wrm\Events\Domain\Repository\DateRepository;
use Wrm\Events\Domain\Repository\EventRepository;
use Wrm\Events\Domain\Repository\OrganizerRepository;
use Wrm\Events\Service\DestinationDataImportService\CategoriesAssignment;
use Wrm\Events\Service\DestinationDataImportService\CategoriesAssignment\Import as CategoryImport;
use Wrm\Events\Service\DestinationDataImportService\DataFetcher;
use Wrm\Events\Service\DestinationDataImportService\DataHandler;
use Wrm\Events\Service\DestinationDataImportService\DataHandler\Assignment;
use Wrm\Events\Service\DestinationDataImportService\DatesFactory;
use Wrm\Events\Service\DestinationDataImportService\Events\CategoriesAssignEvent;
use Wrm\Events\Service\DestinationDataImportService\Events\EventImportEvent;
use Wrm\Events\Service\DestinationDataImportService\FilesAssignment;
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
     * @var FilesAssignment
     */
    private $filesAssignment;

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
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var DataHandler
     */
    private $dataHandler;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * ImportService constructor.
     *
     * @param EventRepository $eventRepository
     * @param OrganizerRepository $organizerRepository
     * @param DateRepository $dateRepository
     * @param ConfigurationManager $configurationManager
     * @param PersistenceManager $persistenceManager
     * @param ObjectManager $objectManager
     * @param DataFetcher $dataFetcher
     * @param FilesAssignment $filesAssignment
     * @param CategoriesAssignment $categoriesAssignment
     * @param LocationAssignment $locationAssignment
     * @param Slugger $slugger
     * @param CacheManager $cacheManager
     * @param DataHandler $dataHandler
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        EventRepository $eventRepository,
        OrganizerRepository $organizerRepository,
        DateRepository $dateRepository,
        ConfigurationManager $configurationManager,
        PersistenceManager $persistenceManager,
        ObjectManager $objectManager,
        DataFetcher $dataFetcher,
        DatesFactory $datesFactory,
        FilesAssignment $filesAssignment,
        CategoriesAssignment $categoriesAssignment,
        LocationAssignment $locationAssignment,
        Slugger $slugger,
        CacheManager $cacheManager,
        DataHandler $dataHandler,
        EventDispatcher $eventDispatcher
    ) {
        $this->eventRepository = $eventRepository;
        $this->organizerRepository = $organizerRepository;
        $this->dateRepository = $dateRepository;
        $this->configurationManager = $configurationManager;
        $this->persistenceManager = $persistenceManager;
        $this->objectManager = $objectManager;
        $this->dataFetcher = $dataFetcher;
        $this->datesFactory = $datesFactory;
        $this->filesAssignment = $filesAssignment;
        $this->categoriesAssignment = $categoriesAssignment;
        $this->locationAssignment = $locationAssignment;
        $this->slugger = $slugger;
        $this->cacheManager = $cacheManager;
        $this->dataHandler = $dataHandler;
        $this->eventDispatcher = $eventDispatcher;
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
            $existingEvent = clone $this->tmpCurrentEvent;

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
                    (bool)$this->getAttributeValue($event, 'DETAILS_ABGESAGT')
                );
            }

            // Set Assets
            if ($event['media_objects'] ?? false) {
                $this->setAssets($event['media_objects']);
            }

            if ($event['source'] ?? false) {
                $this->setSource($event['source']);
            }

            $this->eventDispatcher->dispatch(new EventImportEvent(
                $existingEvent,
                $this->tmpCurrentEvent
            ));

            // Update and persist
            $this->logger->info('Persist database');
            $this->eventRepository->update($this->tmpCurrentEvent);
            $this->persistenceManager->persistAll();

            // Apply changes via DataHandler (The new way)
            $this->logger->info('Apply changes via DataHandler');
            if ($event['categories'] ?? false) {
                $this->setCategories($event['categories']);
            }
            if ($event['features']) {
                $this->setFeatures($event['features']);
            }

            $this->logger->info('Update slugs');
            $this->slugger->update('tx_events_domain_model_event');
            $this->slugger->update('tx_events_domain_model_date');
        }

        $this->logger->info('Flushing cache');
        $this->cacheManager->clearAllCacheTags();

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

        $event = new CategoriesAssignEvent(
            $this->tmpCurrentEvent,
            $categories
        );
        $this->eventDispatcher->dispatch($event);

        $this->dataHandler->storeAssignments(new Assignment(
            $this->tmpCurrentEvent->getUid(),
            'categories',
            $event->getCategories()->toArray()
        ));
    }

    private function setFeatures(array $features): void
    {
        $features = $this->categoriesAssignment->getCategories(new CategoryImport(
            $this->import->getFeaturesParent(),
            $this->import->getFeaturesPid(),
            $features,
            true
        ));

        $this->dataHandler->storeAssignments(new Assignment(
            $this->tmpCurrentEvent->getUid(),
            'features',
            $features->toArray()
        ));
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
            if ($address['rel'] == 'organizer') {
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
            if ($link['rel'] == 'socialmedia' && $link['value'] == 'Facebook') {
                $this->tmpCurrentEvent->setFacebook($link['url']);
            }
            if ($link['rel'] == 'socialmedia' && $link['value'] == 'YouTube') {
                $this->tmpCurrentEvent->setYouTube($link['url']);
            }
            if ($link['rel'] == 'socialmedia' && $link['value'] == 'Instagram') {
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
        $shouldSetPrice = true;
        foreach ($texts as $text) {
            if (isset($text['value']) === false) {
                continue;
            }

            if ($text['rel'] == 'details' && $text['type'] == 'text/plain') {
                $this->tmpCurrentEvent->setDetails(str_replace("\n\n", "\n", $text['value']));
            }
            if ($text['rel'] == 'teaser' && $text['type'] == 'text/plain') {
                $this->tmpCurrentEvent->setTeaser(str_replace("\n\n", "\n", $text['value']));
            }
            if ($shouldSetPrice && $text['rel'] == 'PRICE_INFO_EXTRA' && $text['type'] == 'text/plain') {
                $shouldSetPrice = false;
                $this->tmpCurrentEvent->setPriceInfo(str_replace("\n\n", "\n", $text['value']));
            }
            if ($shouldSetPrice && $text['rel'] == 'PRICE_INFO' && $text['type'] == 'text/plain') {
                $this->tmpCurrentEvent->setPriceInfo(str_replace("\n\n", "\n", $text['value']));
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
        $this->logger->info('Set assets');
        $images = $this->filesAssignment->getImages(
            $this->import,
            $this->tmpCurrentEvent,
            $assets
        );
        $this->tmpCurrentEvent->setImages($images);
    }

    private function setSource(array $source): void
    {
        if (isset($source['value'])) {
            $this->tmpCurrentEvent->setSourceName($source['value']);
        }

        if (isset($source['url'])) {
            $this->tmpCurrentEvent->setSourceUrl($source['url']);
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

        return (bool)$value;
    }
}
