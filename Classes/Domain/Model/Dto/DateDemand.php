<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Domain\Model\Dto;

use DateTimeImmutable;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DateDemand
{
    protected string $sortBy = '';

    protected string $sortOrder = '';

    protected string $categories = '';

    /**
     * @var int[]
     */
    protected array $userCategories = [];

    /**
     * @var int[]
     */
    protected array $features = [];

    protected bool $includeSubCategories = false;

    protected string $categoryCombination = '';

    /**
     * @var int[]
     */
    protected array $regions = [];

    /**
     * @var int[]
     */
    protected array $locations = [];

    /**
     * @var int[]
     */
    protected array $organizers = [];

    protected bool $highlight = false;

    protected string $limit = '';

    protected ?DateTimeImmutable $startObject = null;

    protected ?DateTimeImmutable $endObject = null;

    /**
     * Use midnight as "start".
     */
    protected bool $useMidnight = true;

    /**
     * Only show dates that have not started yet.
     */
    protected bool $upcoming = false;

    protected string $searchword = '';

    /**
     * Synonym1 => ['word1', 'word2', …],
     * Synonym2 => ['word3', 'word4', …],
     * …
     */
    protected array$synonyms = [];

    protected bool $considerDate = false;

    protected string $queryCallback = '';

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
        $this->features = array_map('intval', array_filter($categories));
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

    /**
     * @return int[]
     */
    public function getOrganizers(): array
    {
        return $this->organizers;
    }

    public function setOrganizers(array $organizers): void
    {
        $this->organizers = array_values(array_map('intval', $organizers));
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

    public function getStartObject(): ?DateTimeImmutable
    {
        return $this->startObject;
    }

    /**
     * Returns necessary format for forms.
     *
     * @internal Only for Extbase/Fluid.
     */
    public function getStart(): string
    {
        if ($this->getStartObject() === null) {
            return '';
        }

        return $this->getStartObject()->format('Y-m-d');
    }

    public function setStart(?int $start): void
    {
        if ($start === null) {
            return;
        }
        $this->startObject = new DateTimeImmutable(date('Y-m-d H:i', $start));
    }

    public function getEndObject(): ?DateTimeImmutable
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

    /**
     * Returns necessary format for forms.
     *
     * @internal Only for Extbase/Fluid.
     */
    public function getEnd(): string
    {
        if ($this->getEndObject() === null) {
            return '';
        }

        return $this->getEndObject()->format('Y-m-d');
    }

    public function setEnd(?int $end): void
    {
        if ($end === null) {
            return;
        }

        $this->endObject = new DateTimeImmutable(date('Y-m-d H:i', $end));
    }

    public function setUseMidnight(bool $useMidnight): void
    {
        $this->useMidnight = $useMidnight;
        $this->upcoming = false;
    }

    public function setUpcoming(bool $upcoming): void
    {
        $this->startObject = null;
        $this->endObject = null;
        $this->useMidnight = false;

        $this->upcoming = $upcoming;
    }

    public function shouldShowFromNow(): bool
    {
        return $this->getStartObject() === null
            && $this->getEndObject() === null
            && $this->useMidnight === false
            && $this->upcoming === false;
    }

    public function shouldShowFromMidnight(): bool
    {
        return $this->getStartObject() === null
            && $this->getEndObject() === null
            && $this->useMidnight === true;
    }

    public function shouldShowUpcoming(): bool
    {
        return $this->upcoming === true;
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
