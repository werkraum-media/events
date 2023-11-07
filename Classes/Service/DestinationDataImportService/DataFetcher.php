<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service\DestinationDataImportService;

use Exception;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Log\LogManager;
use WerkraumMedia\Events\Domain\Model\Import;

/**
 * Provides API to fetch data from remote.
 *
 * Only partially migrated from service to here.
 * Further calls need to be migrated.
 */
final class DataFetcher
{
    private readonly LoggerInterface $logger;

    public function __construct(
        private readonly UrlFactory $urlFactory,
        LogManager $logManager,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly GuzzleClientInterface $client
    ) {
        $this->logger = $logManager->getLogger(self::class);
    }

    public function fetchSearchResult(Import $import): array
    {
        $url = $this->urlFactory->createSearchResultUrl($import);

        $this->logger->info('Try to get data from ' . $url);

        if ($this->client instanceof ClientInterface) {
            // Keep after TYPO3 10 was dropped
            $response = $this->client->sendRequest(
                $this->requestFactory->createRequest(
                    'GET',
                    $url
                )
            );
        } else {
            // Drop once TYPO3 10 support was dropped
            $response = $this->client->request(
                'GET',
                $url,
                []
            );
        }

        $jsonContent = $response->getBody()->__toString();

        $jsonResponse = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
        if (is_array($jsonResponse) === false) {
            throw new Exception('No valid JSON fetched, got: "' . $jsonContent . '".', 1639495835);
        }

        $this->logger->info('Received data with ' . count($jsonResponse['items']) . ' items');

        return $jsonResponse;
    }

    public function fetchImage(string $url): ResponseInterface
    {
        // Keep after TYPO3 10 was dropped
        if ($this->client instanceof ClientInterface) {
            return $this->client->sendRequest(
                $this->requestFactory->createRequest(
                    'GET',
                    $url
                )
            );
        }

        // Drop once TYPO3 10 support was dropped
        return $this->client->request(
            'GET',
            $url,
            []
        );
    }
}
