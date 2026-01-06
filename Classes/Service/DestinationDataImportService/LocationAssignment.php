<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service\DestinationDataImportService;

use WerkraumMedia\Events\Domain\Model\Location;
use WerkraumMedia\Events\Domain\Repository\LocationRepositoryInterface;

final class LocationAssignment
{
    public function __construct(
        private readonly LocationRepositoryInterface $repository
    ) {
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
            (string)($event['geo']['main']['latitude'] ?? ''),
            (string)($event['geo']['main']['longitude'] ?? ''),
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
