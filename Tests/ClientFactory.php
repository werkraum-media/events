<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;

/**
 * Must only be used within tests.
 */
class ClientFactory
{
    /**
     * Use this to create a new client which will return provided responses.
     *
     * You can use the $historyContainer to assert actual requests afterwards,
     * e.g. assert that urls or headers match.
     *
     * @see https://susi.dev/mock-http-api-responses-with-guzzle-psr-18-psr-7/
     *
     * @param Response[] $responses
     * @param array{'request': Request, 'response': Response, 'error': null}[] $historyContainer
     */
    public static function createClientWithHistory(
        array $responses,
        array &$historyContainer
    ): Client {
        $history = Middleware::history($historyContainer);

        $handlerStack = HandlerStack::create(new MockHandler($responses));
        $handlerStack->push($history);

        return new Client(['handler' => $handlerStack]);
    }
}
