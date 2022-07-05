<?php

namespace Wrm\Events\Service\DestinationDataImportService\CategoriesAssignment;

use Wrm\Events\Domain\Model\Category;

class Import
{
    /**
     * @var null|Category
     */
    private $parentCategory;

    /**
     * @var int
     */
    private $pid;

    /**
     * @var array
     */
    private $categoryTitles;

    /**
     * @var bool
     */
    private $hideByDefault;

    public function __construct(
        ?Category $parentCategory,
        int $pid,
        array $categoryTitles,
        bool $hideByDefault = false
    ) {
        $this->parentCategory = $parentCategory;
        $this->pid = $pid;
        $this->categoryTitles = $categoryTitles;
        $this->hideByDefault = $hideByDefault;
    }

    public function getParentCategory(): ?Category
    {
        return $this->parentCategory;
    }

    public function getPid(): int
    {
        return $this->pid;
    }

    public function getCategoryTitles(): array
    {
        return $this->categoryTitles;
    }

    public function getHideByDefault(): bool
    {
        return $this->hideByDefault;
    }
}
