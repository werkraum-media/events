<?php

namespace Wrm\Events\Domain\Model\Dto;

use Wrm\Events\Domain\Model\Region;

class EventDemand
{
    /**
     * @var string
     */
    protected $sortBy = '';

    /**
     * @var string
     */
    protected $sortOrder = '';

    /**
     * @var string
     */
    protected $categories = '';

    /**
     * @var bool
     */
    protected $includeSubCategories = false;

    /**
     * @var string
     */
    protected $categoryCombination = '';

    /**
     * @var string
     */
    protected $region = '';

    /**
     * @var bool
     */
    protected $highlight = false;

    /**
     * @var string
     */
    protected $limit = '';

    /**
     * @var array
     */
    protected $recordUids = [];

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function setSortBy(string $sortBy): void
    {
        $this->sortBy = $sortBy;
    }

    public function getSortOrder(): string
    {
        return $this->sortOrder;
    }

    public function setSortOrder(string $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }

    public function getCategories(): string
    {
        return $this->categories;
    }

    public function setCategories(string $categories): void
    {
        $this->categories = $categories;
    }

    public function getIncludeSubCategories(): bool
    {
        return $this->includeSubCategories;
    }

    public function setIncludeSubCategories(bool $includeSubCategories): void
    {
        $this->includeSubCategories = $includeSubCategories;
    }

    public function getCategoryCombination(): string
    {
        return $this->categoryCombination;
    }

    public function setCategoryCombination(string $categoryCombination): void
    {
        $this->categoryCombination = $categoryCombination;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
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

    public function getRecordUids(): array
    {
        return $this->recordUids;
    }

    public function setRecordUids(array $recordUids): void
    {
        $this->recordUids = $recordUids;
    }
}
