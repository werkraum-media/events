<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service\DestinationDataImportService;

final class ArrayBasedConfigurationService implements ConfigurationServiceInterface
{
    public function __construct(
        private readonly array $settings
    ) {
    }

    public function getLicenseKey(): string
    {
        return $this->settings['license'] ?? '';
    }

    public function getRestType(): string
    {
        return $this->settings['restType'] ?? '';
    }

    public function getRestMode(): string
    {
        return $this->settings['restMode'] ?? '';
    }

    public function getRestLimit(): string
    {
        return $this->settings['restLimit'] ?? '';
    }

    public function getRestTemplate(): string
    {
        return $this->settings['restTemplate'] ?? '';
    }

    public function getRestUrl(): string
    {
        return $this->settings['restUrl'] ?? '';
    }
}
