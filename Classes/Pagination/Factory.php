<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Pagination;

use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Core\Pagination\SlidingWindowPagination;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

final class Factory
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
