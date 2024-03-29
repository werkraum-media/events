<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use WerkraumMedia\Events\Domain\Model\Location;

final class LocationRepository extends Repository
{
    public function findOneByGlobalId(string $globalId): ?Location
    {
        $query = $this->createQuery();

        return $query
            ->matching($query->equals('globalId', $globalId))
            ->setLimit(1)
            ->execute()
            ->getFirst()
        ;
    }
}
