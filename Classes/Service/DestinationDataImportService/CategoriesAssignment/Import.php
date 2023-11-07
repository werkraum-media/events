<?php

namespace WerkraumMedia\Events\Service\DestinationDataImportService\CategoriesAssignment;

use WerkraumMedia\Events\Domain\Model\Category;

class Import
{
    /**
     * @var Category|null
     */
    private $parentCategory;

    /**
     * @var int|null
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
        ?int $pid,
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

    public function getPid(): ?int
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
