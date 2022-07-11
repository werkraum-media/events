<?php


namespace Wrm\Events\Events\Controller;

use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use Wrm\Events\Domain\Model\Date;
use Wrm\Events\Domain\Model\Dto\DateDemand;

final class DateListVariables
{
    /**
     * @var array
     */
    private $search;

    /**
     * @var DateDemand
     */
    private $demand;

    /**
     * @var QueryResult<Date>
     */
    private $dates;

    /**
     * @var array
     */
    private $variables = [];

    public function __construct(
        array $search,
        DateDemand $demand,
        QueryResult $dates
    ) {
        $this->search = $search;
        $this->demand = $demand;
        $this->dates = $dates;
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

    /**
     * @param mixed $value
     */
    public function addVariable(string $key, $value): void
    {
        $this->variables[$key] = $value;
    }

    public function getVariablesForView(): array
    {
        return array_merge([
            'search' => $this->search,
            'demand' => $this->demand,
            'dates' => $this->dates,
        ], $this->variables);
    }
}
