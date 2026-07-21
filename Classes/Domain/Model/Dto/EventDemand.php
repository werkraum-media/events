<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Domain\Model\Dto;

class EventDemand
{
    protected string $sortBy = '';

    /**
     * @var int[]
     */
    protected array $categories = [];

    protected bool $highlight = false;

    protected string $limit = '';

    /**
     * @var int[]
     */
    protected array $recordUids = [];

    protected string $searchword = '';

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function setSortBy(string $sortBy): void
    {
        $this->sortBy = $sortBy;
    }

    /**
     * @return int[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param int[] $categories
     */
    public function setCategories(array $categories): void
    {
        $this->categories = array_values(array_map(intval(...), $categories));
    }

    public function getHighlight(): bool
    {
        return $this->highlight;
    }

    public function setHighlight(bool $highlight): void
    {
        $this->highlight = $highlight;
    }

    public function getLimit(): string
    {
        return $this->limit;
    }

    public function setLimit(string $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return int[]
     */
    public function getRecordUids(): array
    {
        return $this->recordUids;
    }

    /**
     * @param int[] $recordUids
     */
    public function setRecordUids(array $recordUids): void
    {
        $this->recordUids = $recordUids;
    }

    public function getSearchword(): string
    {
        return $this->searchword;
    }

    public function setSearchword(string $searchword): void
    {
        $this->searchword = $searchword;
    }

    /**
     * Flat shape for GET URLs (f:link.action / POST redirect); only the
     * visitor-facing fields travel, empties dropped.
     *
     * @return array<string, string|int[]>
     */
    public function getQueryParameters(): array
    {
        $parameters = [];
        if ($this->searchword !== '') {
            $parameters['searchword'] = $this->searchword;
        }
        if ($this->categories !== []) {
            $parameters['categories'] = $this->categories;
        }

        return $parameters;
    }
}
