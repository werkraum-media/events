<?php

namespace Wrm\Events\Service\DestinationDataImportService;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Provides API to fetch data from remote.
 *
 * Only partially migrated from service to here.
 * Further calls need to be migrated.
 */
class DataFetcher
{
    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var GuzzleClientInterface
     */
    private $client;

    public function __construct(
        RequestFactoryInterface $requestFactory,
        GuzzleClientInterface $client
    ) {
        $this->requestFactory = $requestFactory;
        $this->client = $client;
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
