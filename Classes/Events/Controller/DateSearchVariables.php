<?php

namespace Wrm\Events\Events\Controller;

use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use Wrm\Events\Domain\Model\Dto\DateDemand;
use Wrm\Events\Domain\Model\Region;

class DateSearchVariables
{
    /**
     * @var array
     */
    private $settings;

    /**
     * @var array
     */
    private $search;

    /**
     * @var DateDemand
     */
    private $demand;

    /**
     * @var QueryResultInterface<Region>
     */
    private $regions;

    /**
     * @var array<Category>
     */
    private $categories;

    /**
     * @var array<Category>
     */
    private $features;

    /**
     * @var array
     */
    private $variables = [];

    /**
     * @param QueryResultInterface<Region> $regions
     */
    public function __construct(
        array $settings,
        array $search,
        DateDemand $demand,
        QueryResultInterface $regions,
        array $categories,
        array $features
    ) {
        $this->settings = $settings;
        $this->search = $search;
        $this->demand = $demand;
        $this->regions = $regions;
        $this->categories = $categories;
        $this->features = $features;
    }

    public function getSettings(): array
    {
        return $this->settings;
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
     * @return QueryResultInterface<Region>
     */
    public function getRegions(): QueryResultInterface
    {
        return $this->regions;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getFeatures(): array
    {
        return $this->features;
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
            'regions' => $this->regions,
            'categories' => $this->categories,
            'features' => $this->features,
        ], $this->variables);
    }
}
