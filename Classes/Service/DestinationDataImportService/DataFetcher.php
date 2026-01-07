<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service\DestinationDataImportService;

use Exception;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\MathUtility;
use WerkraumMedia\Events\Domain\DestinationData\ImportInterface;

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
        private readonly ClientInterface $client
    ) {
        $this->logger = $logManager->getLogger(self::class);
    }

    public function fetchSearchResult(ImportInterface $import): iterable
    {
        return $this->paginate(
            $this->urlFactory->createSearchResultUrl($import)
        );
    }

    public function fetchImage(string $url): ResponseInterface
    {
        return $this->client->sendRequest(
            $this->requestFactory->createRequest('GET', $url)
        );
    }

    private function fetchItems(string $url): array
    {
        $this->logger->info('Try to get data from ' . $url);

        $response = $this->client->sendRequest(
            $this->requestFactory->createRequest('GET', $url)
        );

        $jsonContent = $response->getBody()->__toString();

        $jsonResponse = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
        if (is_array($jsonResponse) === false) {
            throw new Exception('No valid JSON fetched, got: "' . $jsonContent . '".', 1639495835);
        }

        return $jsonResponse;
    }

    private function paginate(string $url, ?int $remainingCount = null, int $offset = 0): iterable
    {
        if ($remainingCount !== null) {
            $url = $this->adjustToOffset($url, $offset);
        }

        $jsonResponse = $this->fetchItems($url);

        if ($remainingCount === null) {
            $overallCount = MathUtility::forceIntegerInRange($jsonResponse['overallcount'], 0);
            $this->logger->info('Received data with ' . $overallCount . ' items in total');
            if ($overallCount === 0) {
                return;
            }

            $remainingCount = $overallCount;
        }

        $currentCount = count($jsonResponse['items']);
        $this->logger->info('Received data with ' . $currentCount . ' items');
        yield from $jsonResponse['items'];

        if ($currentCount === 0) {
            return;
        }

        $remainingCount -= $currentCount;
        if ($remainingCount <= 0) {
            return;
        }

        $this->logger->info('Received data with ' . $remainingCount . ' items left');
        yield from $this->paginate($url, $remainingCount, $offset + $currentCount);
    }

    private function adjustToOffset(string $url, int $offset): string
    {
        if ($offset === 0) {
            return $url;
        }

        $tempUrl = new Uri($url);
        $queryParams = [];
        parse_str($tempUrl->getQuery(), $queryParams);
        $queryParams['offset'] = $offset;

        return $tempUrl->withQuery(http_build_query($queryParams))->__toString();
    }
}
