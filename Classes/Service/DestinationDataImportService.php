<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service;

use Exception;
use Throwable;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use WerkraumMedia\Events\Caching\CacheManager;
use WerkraumMedia\Events\Domain\Model\Event;
use WerkraumMedia\Events\Domain\Model\Import;
use WerkraumMedia\Events\Domain\Model\Organizer;
use WerkraumMedia\Events\Domain\Model\Region;
use WerkraumMedia\Events\Domain\Repository\DateRepository;
use WerkraumMedia\Events\Domain\Repository\EventRepository;
use WerkraumMedia\Events\Domain\Repository\OrganizerRepository;
use WerkraumMedia\Events\Service\DestinationDataImportService\CategoriesAssignment;
use WerkraumMedia\Events\Service\DestinationDataImportService\CategoriesAssignment\Import as CategoryImport;
use WerkraumMedia\Events\Service\DestinationDataImportService\DataFetcher;
use WerkraumMedia\Events\Service\DestinationDataImportService\DataHandler;
use WerkraumMedia\Events\Service\DestinationDataImportService\DataHandler\Assignment;
use WerkraumMedia\Events\Service\DestinationDataImportService\DatesFactory;
use WerkraumMedia\Events\Service\DestinationDataImportService\Events\CategoriesAssignEvent;
use WerkraumMedia\Events\Service\DestinationDataImportService\Events\EventImportEvent;
use WerkraumMedia\Events\Service\DestinationDataImportService\FilesAssignment;
use WerkraumMedia\Events\Service\DestinationDataImportService\LocationAssignment;
use WerkraumMedia\Events\Service\DestinationDataImportService\Slugger;

final class DestinationDataImportService
{
    private Import $import;

    private Event $tmpCurrentEvent;

    private readonly Logger $logger;

    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly OrganizerRepository $organizerRepository,
        private readonly DateRepository $dateRepository,
        private readonly ConfigurationManager $configurationManager,
        private readonly PersistenceManager $persistenceManager,
        private readonly DataFetcher $dataFetcher,
        private readonly DatesFactory $datesFactory,
        private readonly FilesAssignment $filesAssignment,
        private readonly CategoriesAssignment $categoriesAssignment,
        private readonly LocationAssignment $locationAssignment,
        private readonly Slugger $slugger,
        private readonly CacheManager $cacheManager,
        private readonly DataHandler $dataHandler,
        private readonly EventDispatcher $eventDispatcher,
        LogManager $logManager,
    ) {
        $this->logger = $logManager->getLogger(self::class);
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
        $this->logger->info('Starting Destination Data Import Service');

        try {
            $data = $this->dataFetcher->fetchSearchResult($import);
        } catch (Exception) {
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
        $statusCode = 0;

        foreach ($data['items'] as $event) {
            try {
                $this->importSingleEvent($event, $selectedRegion);
            } catch (Throwable $e) {
                $statusCode = 1;
                $this->logger->error(sprintf(
                    'Error happened while importing event "%s" with global id: %s, got error: %s',
                    $event['title'],
                    $event['global_id'],
                    $e->getMessage(),
                ), [
                    'event' => $event,
                    'exception' => $e,
                ]);

                // Ensure we do not keep broken data.
                $event = $this->eventRepository->findOneBy(['globalId' => $event['global_id']]);
                if ($event instanceof Event) {
                    $this->eventRepository->remove($event);
                    $this->persistenceManager->persistAll();
                }
            }
        }

        $this->logger->info('Flushing cache');
        $this->cacheManager->clearAllCacheTags();

        $this->logger->info('Finished import');
        return $statusCode;
    }

    private function importSingleEvent(
        array $event,
        ?Region $selectedRegion,
    ): void {
        $this->logger->info('Processing event ' . substr((string)$event['title'], 0, 20));

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
        $this->tmpCurrentEvent->setTitle(substr((string)$event['title'], 0, 254));

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
        $eventUid = $this->tmpCurrentEvent->getUid();
        if (is_int($eventUid) === false) {
            throw new Exception('Could not persist and fetch uid of event.', 1701244570);
        }

        $this->logger->info('Apply changes via DataHandler');
        $this->dataHandler->updateEvent(
            $eventUid,
            [
                new Assignment('keywords', implode(', ', $event['keywords'] ?? [])),
                $this->getCategories($event['categories'] ?? []),
                $this->getFeatures($event['features'] ?? []),
            ]
        );

        $this->logger->info('Update slugs');
        $this->slugger->update('tx_events_domain_model_event');
        $this->slugger->update('tx_events_domain_model_date');
    }

    private function getCategories(array $categories): Assignment
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

        return Assignment::createFromDomainObjects(
            'categories',
            $event->getCategories()->toArray()
        );
    }

    private function getFeatures(array $features): Assignment
    {
        $features = $this->categoriesAssignment->getCategories(new CategoryImport(
            $this->import->getFeaturesParent(),
            $this->import->getFeaturesPid(),
            $features,
            true
        ));

        return Assignment::createFromDomainObjects(
            'features',
            $features->toArray()
        );
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

        $dates = $this->datesFactory->createDates($this->import, $timeIntervals, $canceled);
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
                $tmpOrganizer = GeneralUtility::makeInstance(Organizer::class);
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
        $detailText = '';

        foreach ($texts as $text) {
            if (
                isset($text['value']) === false
                || is_string($text['value']) === false
            ) {
                continue;
            }

            $value = str_replace("\n\n", "\n", $text['value']);

            if (
                $text['rel'] == 'details'
                && $text['type'] == 'text/html'
                && $this->import->getFeatures()->hasHtmlForDetailEnabled()
                && $value
            ) {
                $detailText = $value;
                continue;
            }

            if (
                $text['rel'] == 'details'
                && $text['type'] == 'text/plain'
                && (
                    $this->import->getFeatures()->hasHtmlForDetailEnabled() === false
                    || $detailText === ''
                )
            ) {
                $detailText = $value;
                continue;
            }

            if ($text['rel'] == 'teaser' && $text['type'] == 'text/plain') {
                $this->tmpCurrentEvent->setTeaser($value);
                continue;
            }

            if ($shouldSetPrice && $text['rel'] == 'PRICE_INFO_EXTRA' && $text['type'] == 'text/plain') {
                $shouldSetPrice = false;
                $this->tmpCurrentEvent->setPriceInfo($value);
                continue;
            }

            if ($shouldSetPrice && $text['rel'] == 'PRICE_INFO' && $text['type'] == 'text/plain') {
                $this->tmpCurrentEvent->setPriceInfo($value);
                continue;
            }
        }

        $this->tmpCurrentEvent->setDetails($detailText);
    }

    private function getOrCreateEvent(string $globalId, string $title): Event
    {
        $event = $this->eventRepository->findOneBy(['globalId' => $globalId]);

        if ($event instanceof Event) {
            $this->logger->info(
                'Found "' . substr($title, 0, 20) . '..." with global id ' . $globalId . ' in database'
            );
            return $event;
        }

        // New event is created
        $this->logger->info(substr($title, 0, 20) . ' does not exist');
        $event = GeneralUtility::makeInstance(Event::class);
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
