<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Event;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;

class PaginationCacheTest extends AbstractFrontendTestCase
{
    protected function getDataSetFileName(): string
    {
        return 'EventsForPagination.php';
    }

    protected function bodyForListPage(): string
    {
        $request = (new InternalRequest())->withPageId(10);

        return (string)$this->executeFrontendSubRequest($request)->getBody();
    }

    protected function bodyForGeneratedUrl(string $url): string
    {
        $url = html_entity_decode($url);
        $query = [];
        parse_str((string)parse_url($url, PHP_URL_QUERY), $query);

        $request = (new InternalRequest())->withPageId(10)->withQueryParams($query);

        return (string)$this->executeFrontendSubRequest($request)->getBody();
    }

    protected function extractPageTwoUrl(string $html): string
    {
        self::assertMatchesRegularExpression(
            '/href="([^"]*events%5BcurrentPage%5D=2[^"]*)"/',
            $html,
            'No generated page-2 pagination link found in rendered list.'
        );
        preg_match('/href="([^"]*events%5BcurrentPage%5D=2[^"]*)"/', $html, $matches);

        return $matches[1];
    }

    #[Test]
    public function firstPageShowsOnlyConfiguredNumberOfItems(): void
    {
        $body = $this->bodyForListPage();

        self::assertStringContainsString('Event 01', $body);
        self::assertStringContainsString('Event 02', $body);
        self::assertStringNotContainsString('Event 03', $body);
    }

    #[Test]
    public function generatedPageTwoLinkCarriesACacheHash(): void
    {
        $pageTwoUrl = $this->extractPageTwoUrl($this->bodyForListPage());

        self::assertStringContainsString('cHash=', html_entity_decode($pageTwoUrl));
    }

    #[Test]
    public function secondPageShowsNextItems(): void
    {
        $pageTwoUrl = $this->extractPageTwoUrl($this->bodyForListPage());

        $body = $this->bodyForGeneratedUrl($pageTwoUrl);

        self::assertStringContainsString('Event 03', $body);
        self::assertStringContainsString('Event 04', $body);
        self::assertStringNotContainsString('Event 01<', $body);
        self::assertStringNotContainsString('Event 05', $body);
    }

    #[Test]
    public function secondPageIsNotServedFromFirstPageCache(): void
    {
        $firstPage = $this->bodyForListPage();
        self::assertStringContainsString('Event 01', $firstPage);

        $pageTwoUrl = $this->extractPageTwoUrl($firstPage);
        $secondPage = $this->bodyForGeneratedUrl($pageTwoUrl);

        self::assertStringContainsString('Event 03', $secondPage);
        self::assertStringContainsString('Event 04', $secondPage);
        self::assertStringNotContainsString('Event 01<', $secondPage);
    }
}
