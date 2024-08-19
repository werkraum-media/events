<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service\DestinationDataImportService;

use TYPO3\CMS\Core\Http\Uri;
use WerkraumMedia\Events\Domain\Model\Import;

/**
 * Factory to create URLs used during import of Destination Data.
 */
final class UrlFactory
{
    public function __construct(
        private readonly ConfigurationServiceInterface $configuration,
    ) {
    }

    /**
     * URL used to fetch initial set of data.
     */
    public function createSearchResultUrl(
        Import $import
    ): string {
        $parameter = [
            'experience' => $import->getRestExperience(),
            'licensekey' => $this->configuration->getLicenseKey(),
            'type' => $this->configuration->getRestType(),
            'mode' => $this->configuration->getRestMode(),
            'limit' => $this->configuration->getRestLimit(),
            'template' => $this->configuration->getRestTemplate(),
            'q' => $import->getSearchQuery(),
        ];

        $parameter = array_filter($parameter);

        $url = new Uri($this->configuration->getRestUrl());
        $url = $url->withQuery(http_build_query($parameter));
        return (string)$url;
    }
}
