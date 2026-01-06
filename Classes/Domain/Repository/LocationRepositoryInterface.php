<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Domain\Repository;

use WerkraumMedia\Events\Domain\Model\Location;

interface LocationRepositoryInterface
{
    public function findOneByGlobalId(string $globalId): ?Location;
}
