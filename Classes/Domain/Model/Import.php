<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Domain\Model;

use Exception;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use WerkraumMedia\Events\Domain\DestinationData\ImportInterface;
use WerkraumMedia\Events\Domain\Model\Import\Features;

/**
 * Actual request to import.
 * Includes all configuration specific to a concrete import.
 */
class Import extends AbstractDomainObject implements ImportInterface
{
    protected ?int $categoriesPid;

    protected ?int $featuresPid;

    public function __construct(
        protected Folder $filesFolder,
        protected int $storagePid,
        protected string $restLicenseKey,
        protected string $restExperience,
        protected string $restMode = 'next_months,12',
        protected string $restSearchQuery = '',
        int $categoriesPid = 0,
        protected ?Category $categoryParent = null,
        int $featuresPid = 0,
        protected ?Category $featuresParent = null,
        protected ?Region $region = null,
        protected string $importRepeatUntil = '+60 days',
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

    public function getUid(): int
    {
        if ($this->uid > 0) {
            return $this->uid;
        }

        throw new Exception('Should never happen, the UID is not a positive number, but we only fetch stored imports.', 1768225182);
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

    public function getFeatures(): Features
    {
        return new Features($this->importFeatures);
    }

    public function getRepeatUntil(): string
    {
        return $this->importRepeatUntil;
    }

    public function getRestLicenseKey(): string
    {
        return $this->restLicenseKey;
    }

    public function getRestExperience(): string
    {
        return $this->restExperience;
    }

    public function getRestMode(): string
    {
        return $this->restMode;
    }

    public function getRestSearchQuery(): string
    {
        return $this->restSearchQuery;
    }
}
