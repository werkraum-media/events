<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Domain\Model;

use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use WerkraumMedia\Events\Domain\Model\Import\Features;

/**
 * Actual request to import.
 * Includes all configuration specific to a concrete import.
 */
class Import extends AbstractDomainObject
{
    protected ?int $categoriesPid;

    protected ?int $featuresPid;

    public function __construct(
        protected Folder $filesFolder,
        protected int $storagePid,
        protected string $restExperience,
        protected string $restSearchQuery = '',
        int $categoriesPid = 0,
        protected ?Category $categoryParent = null,
        int $featuresPid = 0,
        protected ?Category $featuresParent = null,
        protected ?Region $region = null,
        protected int $importFeatures = 0,
    ) {
        // Do not allow categories on pid 0
        if ($categoriesPid === 0) {
            $categoriesPid = null;
        }
        $this->categoriesPid = $categoriesPid;

        // Do not allow features on pid 0
        if ($featuresPid === 0) {
            $featuresPid = null;
        }
        $this->featuresPid = $featuresPid;
    }

    public function getStoragePid(): int
    {
        return $this->storagePid;
    }

    public function getFilesFolder(): Folder
    {
        return $this->filesFolder;
    }

    public function getCategoriesPid(): ?int
    {
        return $this->categoriesPid;
    }

    public function getCategoryParent(): ?Category
    {
        return $this->categoryParent;
    }

    public function getFeaturesPid(): ?int
    {
        return $this->featuresPid;
    }

    public function getFeaturesParent(): ?Category
    {
        return $this->featuresParent;
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

    public function getFeatures(): Features
    {
        return new Features($this->importFeatures);
    }
}
