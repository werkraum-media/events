<?php

namespace Wrm\Events\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use Wrm\Events\Domain\Model\Location;

class LocationRepository extends Repository
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
