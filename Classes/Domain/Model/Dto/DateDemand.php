<?php

namespace Wrm\Events\Domain\Model\Dto;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class DateDemand
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
     * @var int[]
     */
    protected $userCategories = [];

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
     * Legacy, superceeded by regions which allows multi select / filter.
     */
    protected $region = '';

    /**
     * @var int[]
     */
    protected $regions = [];

    /**
     * @var bool
     */
    protected $highlight = false;

    /**
     * @var string
     */
    protected $limit = '';

    /**
     * @var int|null
     */
    protected $start = null;

    /**
     * @var int|null
     */
    protected $end = null;

    /**
     * @var bool
     */
    protected $useMidnight = true;

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
    protected $considerDate = false;

    /**
     * @var string
     */
    protected $queryCallback = '';

    public static function createFromRequestValues(
        array $submittedValues,
        array $settings
    ): self {
        $instance = new self();
        $instance->setSearchword($submittedValues['searchword'] ?? '');
        $instance->setSynonyms($settings['synonyms'] ?? []);

        $instance->setRegion($submittedValues['region'] ?? '');
        if (isset($submittedValues['regions']) && is_array($submittedValues['regions'])) {
            $instance->setRegions($submittedValues['regions']);
        }

        if ($submittedValues['highlight'] ?? false) {
            $instance->setHighlight($settings['highlight'] ?? false);
        }

        if (isset($submittedValues['start']) && $submittedValues['start'] !== '') {
            $instance->setStart(strtotime($submittedValues['start'] . ' 00:00') ?: null);
        }
        if (isset($submittedValues['end']) && $submittedValues['end'] !== '') {
            $instance->setEnd(strtotime($submittedValues['end'] . ' 23:59') ?: null);
        }
        if (isset($submittedValues['considerDate']) && $submittedValues['considerDate'] !== '') {
            $instance->setConsiderDate((bool)$submittedValues['considerDate']);
        }

        if (isset($submittedValues['userCategories']) && is_array($submittedValues['userCategories'])) {
            $instance->setUserCategories($submittedValues['userCategories']);
        }

        $instance->setSortBy($settings['sortByDate'] ?? '');
        $instance->setSortOrder($settings['sortOrder'] ?? '');
        $instance->setQueryCallback($settings['queryCallback'] ?? '');

        if (!empty($settings['limit'])) {
            $instance->setLimit($settings['limit']);
        }

        return $instance;
    }

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

    /**
     * @param int[] $categories
     */
    public function setUserCategories(array $categories): void
    {
        $this->userCategories = array_map('intval', $categories);
    }

    /**
     * @return int[]
     */
    public function getUserCategories(): array
    {
        return $this->userCategories;
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

    /**
     * @return int[]
     */
    public function getRegions(): array
    {
        return $this->regions;
    }

    public function setRegions(array $regions): void
    {
        $this->regions = array_map('intval', $regions);
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

    public function getSearchword(): string
    {
        return $this->searchword;
    }

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

    public function getStart(): ?int
    {
        return $this->start;
    }

    public function setStart(?int $start): void
    {
        $this->start = $start;
    }

    public function getEnd(): ?int
    {
        return $this->end;
    }

    public function setEnd(?int $end): void
    {
        $this->end = $end;
    }

    public function setUseMidnight(bool $useMidnight): void
    {
        $this->useMidnight = $useMidnight;
    }

    public function shouldShowFromNow(): bool
    {
        return $this->getStart() === null
            && $this->getEnd() === null
            && $this->useMidnight === false;
    }

    public function shouldShowFromMidnight(): bool
    {
        return $this->getStart() === null
            && $this->getEnd() === null
            && $this->useMidnight === true;
    }

    public function getConsiderDate(): bool
    {
        return $this->considerDate;
    }

    public function setConsiderDate(bool $considerDate): void
    {
        $this->considerDate = $considerDate;
    }

    public function getQueryCalback(): string
    {
        return $this->queryCallback;
    }

    public function setQueryCallback(string $queryCallback): void
    {
        $this->queryCallback = $queryCallback;
    }
}
