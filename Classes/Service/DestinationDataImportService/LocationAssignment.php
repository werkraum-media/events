<?php

namespace Wrm\Events\Service\DestinationDataImportService;

use TYPO3\CMS\Core\DataHandling\SlugHelper;
use Wrm\Events\Domain\Model\Location;
use Wrm\Events\Domain\Repository\LocationRepository;

class LocationAssignment
{
    /**
     * @var LocationRepository
     */
    private $repository;

    public function __construct(
        LocationRepository $repository
    ) {
        $this->repository = $repository;
    }

    public function getLocation(array $event): ?Location
    {
        $newLocation = new Location(
            $event['name'] ?? '',
            $event['street'] ?? '',
            $event['zip'] ?? '',
            $event['city'] ?? '',
            $event['district'] ?? '',
            $event['country'] ?? '',
            $event['phone'] ?? '',
            $event['geo']['main']['latitude'] ?? '',
            $event['geo']['main']['longitude'] ?? '',
            $this->createSlug($event['name'] ?? ''),
            -1
        );

        if ($newLocation->isValid() === false) {
            return null;
        }

        $existingLocation = $this->repository->findOneByGlobalId($newLocation->getGlobalId());

        return $existingLocation ?? $newLocation;
    }

    public function createSlug(string $name): string
    {
        $slugHelper = new SlugHelper(
            'tx_events_domain_model_location',
            'slug',
            $GLOBALS['TCA']['tx_events_domain_model_location']['columns']['slug']['config']
        );

        return $slugHelper->sanitize($name);
    }
}
