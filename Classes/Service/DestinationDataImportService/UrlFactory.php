<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service\DestinationDataImportService;

use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use WerkraumMedia\Events\Domain\Model\Import;

/**
 * Factory to create URLs used during import of Destination Data.
 */
final class UrlFactory
{
    /**
     * @var array
     */
    private $settings = [];

    public function __construct(
        BackendConfigurationManager $configurationManager
    ) {
        $this->settings = $configurationManager->getConfiguration(
            'Events',
            'Pi1'
        )['settings']['destinationData'] ?? [];
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
            'q' => $import->getSearchQuery(),
        ];

        $parameter = array_filter($parameter);

        $url = new Uri($this->settings['restUrl']);
        $url = $url->withQuery(http_build_query($parameter));
        return (string)$url;
    }
}
