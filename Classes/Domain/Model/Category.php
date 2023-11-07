<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Domain\Model;

use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;

/**
 * Extend original model to include furher properties.
 *
 * Used for Plugins and Import.
 */
class Category extends AbstractEntity
{
    protected int $sorting = 0;

    /**
     * @var Category|null
     */
    #[Lazy]
    protected $parent;

    public function __construct(
        ?Category $parent,
        int $pid,
        protected string $title,
        protected bool $hidden
    ) {
        $this->parent = $parent;
        $this->pid = $pid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSorting(): int
    {
        return $this->sorting;
    }

    public function getParent(): ?Category
    {
        if ($this->parent instanceof LazyLoadingProxy) {
            $this->parent->_loadRealInstance();
        }
        return $this->parent;
    }
}
