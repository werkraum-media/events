<?php

namespace Wrm\Events\Domain\DestinationData;

/**
 * Actual request to import.
 * Includes all configuration specific to a concrete import.
 */
class Import
{
    /**
     * @var string
     */
    private $restExperience;

    /**
     * @var int
     */
    private $storagePid;

    /**
     * @var int|null
     */
    private $regionUid;

    /**
     * @var string
     */
    private $filesFolder;

    public function __construct(
        string $restExperience,
        int $storagePid,
        ?int $regionUid,
        string $filesFolder
    ) {
        $this->restExperience = $restExperience;
        $this->storagePid = $storagePid;
        $this->regionUid = $regionUid;
        $this->filesFolder = $filesFolder;
    }

    public function getRestExperience(): string
    {
        return $this->restExperience;
    }

    public function getStoragePid(): int
    {
        return $this->storagePid;
    }

    public function getRegionUid(): ?int
    {
        return $this->regionUid;
    }

    public function getFilesFolder(): string
    {
        return $this->filesFolder;
    }
}
