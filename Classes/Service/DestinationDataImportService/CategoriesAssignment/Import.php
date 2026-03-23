<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service\DestinationDataImportService\CategoriesAssignment;

use WerkraumMedia\Events\Domain\Model\Category;

final class Import
{
    /**
     * @param int<0, max>|null $pid
     * @param string[] $categoryTitles
     */
    public function __construct(
        private readonly ?Category $parentCategory,
        private readonly ?int $pid,
        private readonly array $categoryTitles,
        private readonly bool $hideByDefault = false
    ) {
    }

    public function getParentCategory(): ?Category
    {
        return $this->parentCategory;
    }

    /**
     * @return int<0, max>|null
     */
    public function getPid(): ?int
    {
        return $this->pid;
    }

    /**
     * @return string[]
     */
    public function getCategoryTitles(): array
    {
        return $this->categoryTitles;
    }

    public function getHideByDefault(): bool
    {
        return $this->hideByDefault;
    }
}
