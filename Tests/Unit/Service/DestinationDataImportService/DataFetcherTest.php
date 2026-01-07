<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Unit\Service\DestinationDataImportService;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Log\LogManager;
use WerkraumMedia\Events\Domain\DestinationData\ImportInterface;
use WerkraumMedia\Events\Service\DestinationDataImportService\DataFetcher;
use WerkraumMedia\Events\Service\DestinationDataImportService\UrlFactory;

#[CoversClass(DataFetcher::class)]
#[UsesClass(UrlFactory::class)]
final class DataFetcherTest extends TestCase
{
    #[Test]
    public function returnsAllItemsOnFirstRequest(): void
    {
        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $this->assertRequestsToUrls(
            $requestFactory,
            'http://meta.et4.de/rest.ashx/search/?type=Event&template=ET2014A.json&limit=500&sort=globalid+asc%2C+title+asc',
        );

        $client = self::createStub(ClientInterface::class);
        $client->method('sendRequest')->willReturn(new JsonResponse([
            'status' => 'OK',
            'count' => 100,
            'overallcount' => 100,
            'items' => range(1, 100),
        ]));

        $result = $this->callFetchSearchResult($requestFactory, $client);

        self::assertNumberOfItems(100, $result);
    }

    #[Test]
    public function returnsAllItemsWithTwoRequests(): void
    {
        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $this->assertRequestsToUrls(
            $requestFactory,
            'http://meta.et4.de/rest.ashx/search/?type=Event&template=ET2014A.json&limit=500&sort=globalid+asc%2C+title+asc',
            'http://meta.et4.de/rest.ashx/search/?type=Event&template=ET2014A.json&limit=500&sort=globalid+asc%2C+title+asc&offset=500',
        );

        $client = self::createStub(ClientInterface::class);
        $client->method('sendRequest')->willReturnOnConsecutiveCalls(
            new JsonResponse([
                'status' => 'OK',
                'count' => 500,
                'overallcount' => 600,
                'items' => range(1, 500),
            ]),
            new JsonResponse([
                'status' => 'OK',
                'count' => 100,
                'overallcount' => 500,
                'items' => range(501, 600),
            ])
        );

        $result = $this->callFetchSearchResult($requestFactory, $client);

        self::assertNumberOfItems(600, $result);
    }

    #[Test]
    public function handlesSituationWhereConsecutiveRequestDoesNotReturnAnythingAnymore(): void
    {
        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $this->assertRequestsToUrls(
            $requestFactory,
            'http://meta.et4.de/rest.ashx/search/?type=Event&template=ET2014A.json&limit=500&sort=globalid+asc%2C+title+asc',
            'http://meta.et4.de/rest.ashx/search/?type=Event&template=ET2014A.json&limit=500&sort=globalid+asc%2C+title+asc&offset=500',
        );

        $client = self::createStub(ClientInterface::class);
        $client->method('sendRequest')->willReturnOnConsecutiveCalls(
            new JsonResponse([
                'status' => 'OK',
                'count' => 500,
                'overallcount' => 600,
                'items' => range(1, 500),
            ]),
            new JsonResponse([
                'status' => 'OK',
                'count' => 0,
                'overallcount' => 500,
                'items' => [],
            ])
        );

        $result = $this->callFetchSearchResult($requestFactory, $client);

        self::assertNumberOfItems(500, $result);
    }

    /**
     * @param MockObject&RequestFactoryInterface $requestFactory
     * @param Stub&ClientInterface $client
     */
    private function callFetchSearchResult(
        MockObject $requestFactory,
        Stub $client,
    ): iterable {
        $subject = new DataFetcher(
            new UrlFactory(),
            self::createStub(LogManager::class),
            $requestFactory,
            $client
        );

        return $subject->fetchSearchResult(self::createStub(ImportInterface::class));
    }

    /**
     * @param MockObject&RequestFactoryInterface $requestFactory
     */
    private function assertRequestsToUrls(MockObject $requestFactory, string ... $expectedUrls): void
    {
        $requestFactory
            ->expects(self::exactly(count($expectedUrls)))
            ->method('createRequest')
            ->with('GET', self::callback(function (string $url) use ($expectedUrls): bool {
                static $call = 0;
                return $url === $expectedUrls[$call++];
            }))
        ;
    }

    private static function assertNumberOfItems(int $expectedCount, iterable $items): void
    {
        $count = 0;
        foreach ($items as $item) {
            ++$count;
        }

        self::assertSame($expectedCount, $count);
    }
}
