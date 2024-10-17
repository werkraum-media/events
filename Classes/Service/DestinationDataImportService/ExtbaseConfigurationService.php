<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service\DestinationDataImportService;

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use WerkraumMedia\Events\Service\ExtbaseConfigurationManagerService;

final class ExtbaseConfigurationService implements ConfigurationServiceInterface
{
    private array $settings = [];

    public function __construct(
        private ExtbaseConfigurationManagerService $configurationManager
    ) {
    }

    public function getLicenseKey(): string
    {
        return $this->getSettings()['license'] ?? '';
    }

    public function getRestType(): string
    {
        return $this->getSettings()['restType'] ?? '';
    }

    public function getRestMode(): string
    {
        return $this->getSettings()['restMode'] ?? '';
    }

    public function getRestLimit(): string
    {
        return $this->getSettings()['restLimit'] ?? '';
    }

    public function getRestTemplate(): string
    {
        return $this->getSettings()['restTemplate'] ?? '';
    }

    public function getRestUrl(): string
    {
        return $this->getSettings()['restUrl'] ?? '';
    }

    private function getSettings(): array
    {
        if ($this->settings !== []) {
            return $this->settings;
        }

        $fullTypoScript = $this->configurationManager
            ->getInstanceWithBackendContext()
            ->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT)
        ;

        $this->settings = $fullTypoScript['module.']['tx_events.']['settings.']['destinationData.']
            ?? $fullTypoScript['module.']['tx_events_pi1.']['settings.']['destinationData.']
            ?? [];

        return $this->settings;
    }
}
