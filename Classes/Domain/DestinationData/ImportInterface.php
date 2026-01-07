<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Domain\DestinationData;

interface ImportInterface
{
    public function getRestExperience(): string;
    public function getRestLicenseKey(): string;
    public function getRestMode(): string;
    public function getRestSearchQuery(): string;
}
