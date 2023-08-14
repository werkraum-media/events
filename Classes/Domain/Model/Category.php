<?php

namespace Wrm\Events\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;

/**
 * Extend original model to include furher properties.
 *
 * Used for Plugins and Import.
 */
class Category extends AbstractEntity
{
    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var int
     */
    protected $sorting = 0;

    /**
     * @var bool
     */
    protected $hidden = false;

    /**
     * @var Category|null
     *
     * @Extbase\ORM\Lazy
     */
    protected $parent;

    /**
     * @param Category|null $parent
     */
    public function __construct(
        $parent,
        int $pid,
        string $title,
        bool $hidden
    ) {
        $this->parent = $parent;
        $this->pid = $pid;
        $this->title = $title;
        $this->hidden = $hidden;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSorting(): int
    {
        return $this->sorting;
    }

    /**
     * @return Category|null
     */
    public function getParent()
    {
        if ($this->parent instanceof LazyLoadingProxy) {
            $this->parent->_loadRealInstance();
        }
        return $this->parent;
    }
}
