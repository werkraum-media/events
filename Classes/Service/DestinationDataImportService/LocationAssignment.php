<?php

namespace Wrm\Events\Service\DestinationDataImportService;

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
            -1
        );

        if ($newLocation->isValid() === false) {
            return null;
        }

        $existingLocation = $this->repository->findOneByGlobalId($newLocation->getGlobalId());

        if ($existingLocation === null) {
            return $newLocation;
        }

        $existingLocation->updateFromLocation($newLocation);

        return $existingLocation;
    }
}
