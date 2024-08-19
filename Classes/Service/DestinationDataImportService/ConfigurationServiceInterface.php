<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service\DestinationDataImportService;

interface ConfigurationServiceInterface
{
    public function getLicenseKey(): string;
    public function getRestType(): string;
    public function getRestMode(): string;
    public function getRestLimit(): string;
    public function getRestTemplate(): string;
    public function getRestUrl(): string;
}
