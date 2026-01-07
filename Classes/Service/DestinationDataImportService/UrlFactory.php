<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service\DestinationDataImportService;

use TYPO3\CMS\Core\Http\Uri;
use WerkraumMedia\Events\Domain\DestinationData\ImportInterface;
use WerkraumMedia\Events\Domain\Model\Import;

/**
 * Factory to create URLs used during import of Destination Data.
 */
final class UrlFactory
{
    /**
     * URL used to fetch initial set of data.
     */
    public function createSearchResultUrl(
        ImportInterface $import,
        int $offset = 0
    ): string {
        $parameter = [
            'experience' => $import->getRestExperience(),
            'licensekey' => $import->getRestLicenseKey(),
            'type' => 'Event',
            'mode' => $import->getRestMode(),
            'template' => 'ET2014A.json',
            'q' => $import->getRestSearchQuery(),
            'limit' => 500,
            // Used to have a defined order when fetching data
            'sort' => 'globalid asc, title asc',
        ];

        $parameter = array_filter($parameter);

        $url = new Uri('http://meta.et4.de/rest.ashx/search/');
        $url = $url->withQuery(http_build_query($parameter));
        return (string)$url;
    }
}
