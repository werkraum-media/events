<?php

namespace Wrm\Events\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\Category as ExtbaseCategory;

/**
 * Extend original model to include furher properties.
 *
 * Used for Plugins and Import.
 */
class Category extends ExtbaseCategory
{
    /**
     * @var int
     */
    protected $sorting = 0;

    /**
     * @var bool
     */
    protected $hidden = false;

    public function getSorting(): int
    {
        return $this->sorting;
    }

    public function hide(): void
    {
        $this->hidden = true;
    }
}
