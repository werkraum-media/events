<?php

namespace Wrm\Events\Domain\Model;

use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;

/**
 * Actual request to import.
 * Includes all configuration specific to a concrete import.
 */
class Import extends AbstractDomainObject
{
    /**
     * @var int
     */
    protected $storagePid;

    /**
     * @var Folder
     */
    protected $filesFolder;

    /**
     * @var int
     */
    protected $categoriesPid;

    /**
     * @var Category|null
     */
    protected $categoryParent;

    /**
     * @var Region|null
     */
    protected $region;

    /**
     * @var string
     */
    protected $restExperience;

    /**
     * @var string
     */
    protected $restSearchQuery;

    public function __construct(
        Folder $filesFolder,
        int $storagePid,
        string $restExperience,
        string $restSearchQuery = '',
        int $categoriesPid = 0,
        ?Category $categoryParent = null,
        ?Region $region = null
    ) {
        $this->filesFolder = $filesFolder;
        $this->storagePid = $storagePid;

        $this->categoriesPid = $categoriesPid;
        $this->categoryParent = $categoryParent;

        $this->restExperience = $restExperience;
        $this->restSearchQuery = $restSearchQuery;

        $this->region = $region;
    }

    public function getStoragePid(): int
    {
        return $this->storagePid;
    }

    public function getFilesFolder(): Folder
    {
        return $this->filesFolder;
    }

    public function getCategoriesPid(): int
    {
        return $this->categoriesPid;
    }

    public function getCategoryParent(): ?Category
    {
        return $this->categoryParent;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function getRestExperience(): string
    {
        return $this->restExperience;
    }

    public function getSearchQuery(): string
    {
        return $this->restSearchQuery;
    }
}
