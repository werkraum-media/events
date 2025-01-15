<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Frontend;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Context\DateTimeAspect;
use TYPO3\CMS\Core\Http\StreamFactory;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;

final class SearchTest extends AbstractFrontendTestCase
{
    protected function setUp(): void
    {
        $this->testExtensionsToLoad = [
            ...$this->testExtensionsToLoad,
            'typo3conf/ext/events/Tests/Functional/Frontend/Fixtures/Extensions/ce_filter/',
            'typo3conf/ext/events/Tests/Functional/Frontend/Fixtures/Extensions/ce_list/',
        ];

        ArrayUtility::mergeRecursiveWithOverrule($this->configurationToUseInTestInstance, [
            'FE' => [
                'cacheHash' => [
                    'excludedParameters' => [
                        '^events[search]',
                    ],
                ],
            ],
        ]);

        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SearchSetup.php');
    }

    #[Test]
    public function submittingPostWithoutSearchArgumentsRedirectsToGet(): void
    {
        $request = new InternalRequest('https://example.com/');
        $request = $request->withMethod('POST');
        $request = $request->withPageId(1);

        $response = $this->executeFrontendSubRequest($request);

        self::assertSame(303, $response->getStatusCode());
        self::assertSame('http://example.com/', $response->getHeaderLine('location'));
    }

    #[Test]
    public function submittingPostWithSearchArgumentsRedirectsToGet(): void
    {
        $request = new InternalRequest('https://example.com/');
        $request = $request->withMethod('POST');
        $request = $request->withPageId(1);
        $request = $request->withBody((new StreamFactory())->createStream(http_build_query([
            'events' => [
                'search' => [
                    'searchword' => 'Event',
                ],
            ],
        ])));

        $response = $this->executeFrontendSubRequest($request);

        self::assertSame(303, $response->getStatusCode());
        self::assertSame('http://example.com/?events%5Bsearch%5D%5Bsearchword%5D=Event', $response->getHeaderLine('location'));
    }

    #[Test]
    public function submittedInputShownInForm(): void
    {
        $request = new InternalRequest('https://example.com/');
        $request = $request->withQueryParams([
            'events' => [
                'search' => [
                    'searchword' => 'Event',
                ],
            ],
        ]);
        $request = $request->withPageId(1);
        $response = $this->executeFrontendSubRequest($request);
        $html = $response->getBody()->__toString();

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('value="Event"', $html, 'Submitted value is not rendered within form');
    }

    #[Test]
    public function submittedInputIsKeptWithinPagination(): void
    {
        $request = new InternalRequest('https://example.com/');
        $request = $request->withAttribute('testingDateAspect', new DateTimeAspect(new DateTimeImmutable('2022-08-10')));
        $request = $request->withQueryParams([
            'events' => [
                'search' => [
                    'searchword' => 'Event',
                ],
            ],
        ]);
        $request = $request->withPageId(1);
        $response = $this->executeFrontendSubRequest($request);
        $html = $response->getBody()->__toString();

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('Event one', $html);
        self::assertStringContainsString('Current Page 1', $html);
        self::assertStringContainsString('/page-2?events%5Bsearch%5D%5Bsearchword%5D=Event&amp;cHash=41711281293c1c3a3aa161e96bbd4e98', $html);
        self::assertStringNotContainsString('Event two', $html);

        self::assertStringContainsString('value="Event"', $html, 'Submitted value is not rendered within form');

        // Ensure going to 2nd page works (make sure it is available after warming up cache for first page)

        $request = new InternalRequest('https://example.com/');
        $request = $request->withAttribute('testingDateAspect', new DateTimeAspect(new DateTimeImmutable('2022-08-10')));
        $request = $request->withQueryParams([
            'events' => [
                'search' => [
                    'searchword' => 'Event',
                ],
                'controller' => 'Date',
                'currentPage' => '2',
            ],
            'cHash' => '41711281293c1c3a3aa161e96bbd4e98',
        ]);
        $request = $request->withPageId(1);
        $response = $this->executeFrontendSubRequest($request);
        $html = $response->getBody()->__toString();

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('Event two', $html);
        self::assertStringContainsString('Current Page 2', $html);
        self::assertStringContainsString('/page-1?events%5Bsearch%5D%5Bsearchword%5D=Event&amp;cHash=13c33adfef09ccb19da7d399ada25c4c', $html);
        self::assertStringNotContainsString('Event one', $html);

        self::assertStringContainsString('value="Event"', $html, 'Submitted value is not rendered within form');
    }
}
