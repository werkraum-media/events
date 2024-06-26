<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Events\Controller;

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use WerkraumMedia\Events\Domain\Model\Category;
use WerkraumMedia\Events\Domain\Model\Dto\DateDemand;
use WerkraumMedia\Events\Domain\Model\Region;

final class DateSearchVariables
{
    private array $variables = [];

    /**
     * @param QueryResultInterface<Region> $regions
     * @param array<Category> $categories
     * @param array<Category> $features
     */
    public function __construct(
        private readonly array $settings,
        private readonly array $search,
        private readonly DateDemand $demand,
        private readonly QueryResultInterface $regions,
        private readonly array $categories,
        private readonly array $features
    ) {
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

    public function addVariable(string $key, mixed $value): void
    {
        $this->variables[$key] = $value;
    }

    public function getVariablesForView(): array
    {
        return [
            'search' => $this->search,
            'demand' => $this->demand,
            'regions' => $this->regions,
            'categories' => $this->categories,
            'features' => $this->features,
            ...$this->variables,
        ];
    }
}
