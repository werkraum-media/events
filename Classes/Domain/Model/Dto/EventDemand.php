<?php

namespace Wrm\Events\Domain\Model\Dto;

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
    protected $region = null;

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

    /**
     * @return string
     */
    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    /**
     * @param string $sortBy
     */
    public function setSortBy(string $sortBy)
    {
        $this->sortBy = $sortBy;
    }

    /**
     * @return string
     */
    public function getSortOrder(): string
    {
        return $this->sortOrder;
    }

    /**
     * @param string $sortOrder
     */
    public function setSortOrder(string $sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }

    /**
     * @return string
     */
    public function getCategories(): string
    {
        return $this->categories;
    }

    /**
     * @param string $categories
     */
    public function setCategories(string $categories)
    {
        $this->categories = $categories;
    }

    /**
     * @return bool
     */
    public function getIncludeSubCategories(): bool
    {
        return $this->includeSubCategories;
    }

    /**
     * @param bool $includeSubCategories
     */
    public function setIncludeSubCategories(bool $includeSubCategories)
    {
        $this->includeSubCategories = $includeSubCategories;
    }

    /**
     * @return string
     */
    public function getCategoryCombination(): string
    {
        return $this->categoryCombination;
    }

    /**
     * @param string $categoryCombination
     */
    public function setCategoryCombination(string $categoryCombination)
    {
        $this->categoryCombination = $categoryCombination;
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * @param \Wrm\DdEvents\Domain\Model\Region $region
     */
    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    /**
     * @return bool
     */
    public function getHighlight(): bool
    {
        return $this->highlight;
    }

    /**
     * @param bool $hightlight
     */
    public function setHighlight(bool $highlight): void
    {
        $this->highlight = $highlight;
    }

    /**
     * @return string
     */
    public function getLimit(): string
    {
        return $this->limit;
    }

    /**
     * @param string $limit
     */
    public function setLimit(string $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return array
     */
    public function getRecordUids(): array
    {
        return $this->recordUids;
    }

    /**
     * @param array $recordUids
     */
    public function setRecordUids(array $recordUids): void
    {
        $this->recordUids = $recordUids;
    }
}
