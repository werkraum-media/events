<?php

namespace Wrm\Events\Service\DestinationDataImportService;

use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use Wrm\Events\Domain\Model\Import;

/**
 * Factory to create URLs used during import of Destination Data.
 */
class UrlFactory
{
    /**
     * @var array
     */
    private $settings = [];

    public function __construct(
        ConfigurationManager $configurationManager
    ) {
        $this->settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'Events',
            'Pi1'
        )['destinationData'] ?? [];
    }

    /**
     * URL used to fetch initial set of data.
     */
    public function createSearchResultUrl(
        Import $import
    ): string {
        $parameter = [
            'experience' => $import->getRestExperience(),
            'licensekey' => $this->settings['license'] ?? '',
            'type' => $this->settings['restType'] ?? '',
            'mode' => $this->settings['restMode'] ?? '',
            'limit' => $this->settings['restLimit'] ?? '',
            'template' => $this->settings['restTemplate'] ?? '',
            'q' => $import->getSearchQuery()
        ];

        $parameter = array_filter($parameter);

        $url = new Uri($this->settings['restUrl']);
        $url = $url->withQuery(http_build_query($parameter));
        return (string) $url;
    }
}
