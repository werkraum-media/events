<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Events\Controller;

use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use WerkraumMedia\Events\Domain\Model\Date;
use WerkraumMedia\Events\Domain\Model\Dto\DateDemand;

final class DateListVariables
{
    private array $variables = [];

    public function __construct(
        private readonly array $search,
        private readonly DateDemand $demand,
        /**
         * @var QueryResult<Date>
         */
        private readonly QueryResult $dates,
        private readonly PaginationInterface $pagination
    ) {
    }

    public function getSearch(): array
    {
        return $this->search;
    }

    public function getDemand(): DateDemand
    {
        return $this->demand;
    }

    /**
     * @return QueryResult<Date>
     */
    public function getDates(): QueryResult
    {
        return $this->dates;
    }

    public function addVariable(string $key, mixed $value): void
    {
        $this->variables[$key] = $value;
    }

    public function getVariablesForView(): array
    {
        return [
            'search' => $this->search,
            'demand' => $this->demand,
            'dates' => $this->dates,
            'pagination' => $this->pagination,
            ...$this->variables,
        ];
    }
}
