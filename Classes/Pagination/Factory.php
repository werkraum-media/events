<?php

declare(strict_types=1);

namespace Wrm\Events\Pagination;

use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use Wrm\Events\Backports\V12\Pagination\SlidingWindowPagination;

class Factory
{
    public function create(
        int $currentPage,
        int $itemsPerPage,
        int $maximumLinks,
        QueryResultInterface $items
    ): PaginationInterface {
        return new SlidingWindowPagination(
            new QueryResultPaginator($items, $currentPage, $itemsPerPage),
            $maximumLinks
        );
    }
}
