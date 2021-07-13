<?php

namespace Wrm\Events\Domain\Model\Dto;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class DateDemand {

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
    protected $highlight = 0;

    /**
     * @var string
     */
    protected $limit = '';

    /**
     * @var string
     */
    protected $start = '';

    /**
     * @var string
     */
    protected $end = '';

    /**
     * @var string
     */
    protected $searchword = '';

    /**
     * @var array
     *
     * Synonym1 => ['word1', 'word2', …],
     * Synonym2 => ['word3', 'word4', …],
     * …
     */
    protected $synonyms = [];

    /**
     * @var bool
     */
    protected $considerDate = 0;

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
     * @param bool $highlight
     */
    public function setHighlight(string $highlight): void
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
     * @return string
     */
    public function getSearchword(): string
    {
        return $this->searchword;
    }

    /**
     * @param string $searchword
     */
    public function setSearchword(string $searchword): void
    {
        $this->searchword = $searchword;
    }

    /**
     * @param array $synonyms
     * [
     *  [
     *   'word' => 'Word1',
     *   'synonyms' => 'synonym1, synonym2',
     *  ],
     *  [
     *   'word' => 'Word2',
     *   'synonyms' => 'synonym3, synonym4',
     *  ],
     *  …
     * ]
     */
    public function setSynonyms(array $synonyms): void
    {
        $this->synonyms = [];
        foreach ($synonyms as $config) {
            $synonymsForWord = GeneralUtility::trimExplode(',', $config['synonyms'], true);
            foreach ($synonymsForWord as $synonym) {
                $synonym = mb_strtolower($synonym);
                $this->synonyms[$synonym][] = $config['word'];
                $this->synonyms[$synonym] = array_unique($this->synonyms[$synonym]);
            }
        }
    }

    public function getSynonymsForSearchword(): array
    {
        $searchWord = mb_strtolower($this->getSearchword());
        return $this->synonyms[$searchWord] ?? [];
    }

    /**
     * @return string
     */
    public function getStart(): string
    {
        return $this->start;
    }

    /**
     * @param string $start
     */
    public function setStart(string $start): void
    {
        $this->start = $start;
    }

    /**
     * @return string
     */
    public function getEnd(): string
    {
        return $this->end;
    }

    /**
     * @param string $end
     */
    public function setEnd(string $end): void
    {
        $this->end = $end;
    }

    /**
     * @return bool
     */
    public function getConsiderDate(): bool
    {
        return $this->considerDate;
    }

    /**
     * @param bool $considerDate
     */
    public function setConsiderDate(string $considerDate): void
    {
        $this->considerDate = $considerDate;
    }

}