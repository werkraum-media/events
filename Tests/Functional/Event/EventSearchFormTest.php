<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Event;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Http\StreamFactory;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;

class EventSearchFormTest extends AbstractFrontendTestCase
{
    protected function getDataSetFileName(): string
    {
        return 'EventsForSearchForm.php';
    }

    #[Test]
    public function searchFormRendersWithEditorCategories(): void
    {
        $request = (new InternalRequest('https://example.com/list/'))->withPageId(10);

        $body = (string)$this->executeFrontendSubRequest($request)->getBody();

        self::assertStringContainsString('name="events[search][searchword]"', $body);
        self::assertStringContainsString('Museum', $body);
        self::assertStringContainsString('Kirche', $body);
        self::assertStringContainsString('value="10"', $body);
        self::assertStringContainsString('value="11"', $body);
    }

    #[Test]
    public function postRedirectsToGet(): void
    {
        $request = (new InternalRequest('https://example.com/list/'))
            ->withPageId(10)
            ->withMethod('POST')
            ->withBody((new StreamFactory())->createStream(http_build_query([
                'events' => ['search' => ['searchword' => 'Domberg']],
            ])))
        ;

        $response = $this->executeFrontendSubRequest($request);

        self::assertSame(303, $response->getStatusCode());
        self::assertStringContainsString(
            'events%5Bsearch%5D%5Bsearchword%5D=Domberg',
            $response->getHeaderLine('location')
        );
    }

    #[Test]
    public function searchwordFiltersTheList(): void
    {
        $request = (new InternalRequest('https://example.com/list/'))
            ->withPageId(10)
            ->withQueryParameters(['events[search][searchword]' => 'Domberg'])
        ;

        $body = (string)$this->executeFrontendSubRequest($request)->getBody();

        self::assertStringContainsString('Domberg Erfurt', $body);
        self::assertStringNotContainsString('Goethehaus Weimar', $body);
        self::assertStringNotContainsString('Stadtmuseum Erfurt', $body);
    }

    #[Test]
    public function categorySelectionFiltersTheList(): void
    {
        $request = (new InternalRequest('https://example.com/list/'))
            ->withPageId(10)
            ->withQueryParameters(['events[search][userCategories][0]' => '10'])
        ;

        $body = (string)$this->executeFrontendSubRequest($request)->getBody();

        self::assertStringContainsString('Goethehaus Weimar', $body);
        self::assertStringNotContainsString('Domberg Erfurt', $body);
        self::assertStringNotContainsString('Stadtmuseum Erfurt', $body);
    }

    #[Test]
    public function submittedSearchwordRepopulatesTheForm(): void
    {
        $request = (new InternalRequest('https://example.com/list/'))
            ->withPageId(10)
            ->withQueryParameters(['events[search][searchword]' => 'Domberg'])
        ;

        $body = (string)$this->executeFrontendSubRequest($request)->getBody();

        self::assertStringContainsString('value="Domberg"', $body);
    }

    #[Test]
    public function submittedCategoryStaysChecked(): void
    {
        $request = (new InternalRequest('https://example.com/list/'))
            ->withPageId(10)
            ->withQueryParameters(['events[search][userCategories][0]' => '10'])
        ;

        $body = (string)$this->executeFrontendSubRequest($request)->getBody();

        self::assertMatchesRegularExpression('/value="10"[^>]*checked/', $body);
    }
}
