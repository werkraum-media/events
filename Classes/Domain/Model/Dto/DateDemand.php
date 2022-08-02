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
     * @var int[]
     */
    protected $features = [];

    /**
     * @var bool
     */
    protected $includeSubCategories = false;

    /**
     * @var string
     */
    protected $categoryCombination = '';

    /**
     * @var int[]
     */
    protected $regions = [];

    /**
     * @var int[]
     */
    protected $locations = [];

    /**
     * @var bool
     */
    protected $highlight = false;

    /**
     * @var string
     */
    protected $limit = '';

    /**
     * @var null|\DateTimeImmutable
     */
    protected $startObject = null;

    /**
     * @var null|\DateTimeImmutable
     */
    protected $endObject = null;

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

        if (isset($submittedValues['locations']) && is_array($submittedValues['locations'])) {
            $instance->setLocations($submittedValues['locations']);
        }

        $instance->setRegions(GeneralUtility::intExplode(',', $submittedValues['region'] ?? '', true));
        if (isset($submittedValues['regions']) && is_array($submittedValues['regions'])) {
            $instance->setRegions($submittedValues['regions']);
        }

        if ($submittedValues['highlight'] ?? false) {
            $instance->setHighlight(true);
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

        if (isset($submittedValues['features']) && is_array($submittedValues['features'])) {
            $instance->setFeatures($submittedValues['features']);
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

    /**
     * @param int[] $categories
     */
    public function setFeatures(array $categories): void
    {
        $this->features = array_map('intval', $categories);
    }

    /**
     * @return int[]
     */
    public function getFeatures(): array
    {
        return $this->features;
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
        return implode(',', $this->regions);
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

    /**
     * @return int[]
     */
    public function getLocations(): array
    {
        return $this->locations;
    }

    public function setLocations(array $locations): void
    {
        $this->locations = array_map('intval', $locations);
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

    public function getStartObject(): ?\DateTimeImmutable
    {
        return $this->startObject;
    }

    public function getStart(): ?int
    {
        if ($this->getStartObject() === null) {
            return null;
        }

        return (int) $this->getStartObject()->format('U');
    }

    public function setStart(?int $start): void
    {
        if ($start === null) {
            return;
        }
        $this->startObject = new \DateTimeImmutable(date('Y-m-d H:i', $start));
    }

    public function getEndObject(): ?\DateTimeImmutable
    {
        return $this->endObject;
    }

    public function getEndsOnSameDay(): bool
    {
        if ($this->getStartObject() === null || $this->getEndObject() === null) {
            return true;
        }

        return $this->getStartObject()->format('Y-m-d') === $this->getEndObject()->format('Y-m-d');
    }

    public function getEnd(): ?int
    {
        if ($this->getEndObject() === null) {
            return null;
        }

        return (int) $this->getEndObject()->format('U');
    }

    public function setEnd(?int $end): void
    {
        if ($end === null) {
            return;
        }

        $this->endObject = new \DateTimeImmutable(date('Y-m-d H:i', $end));
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
