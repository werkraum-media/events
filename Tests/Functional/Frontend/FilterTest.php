<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Frontend;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;

final class FilterTest extends AbstractFrontendTestCase
{
    #[Test]
    public function canFilterByASingleLocationViaFlexform(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/FilterByASingleLocationViaFlexform.php');

        $request = new InternalRequest('https://example.com/');
        $request = $request->withPageId(1);
        $response = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

        self::assertStringNotContainsString('Lotte in Weimar', $html);
        self::assertStringContainsString('Was hat das Universum mit mir zu tun?', $html);
    }

    #[Test]
    public function canFilterByTwoLocationsViaFlexform(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/FilterByTwoLocationsViaFlexform.php');

        $request = new InternalRequest('https://example.com/');
        $request = $request->withPageId(1);
        $response = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

        self::assertStringContainsString('Lotte in Weimar', $html);
        self::assertStringContainsString('Was hat das Universum mit mir zu tun?', $html);
    }

    #[Test]
    public function canFilterDatesByParentLocationViaFlexform(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/FilterDatesByParentLocationViaFlexform.php');

        $request = new InternalRequest('https://example.com/');
        $request = $request->withPageId(1);
        $response = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

        self::assertStringContainsString('Lotte in Weimar', $html);
        self::assertStringContainsString('Was hat das Universum mit mir zu tun?', $html);
    }

    /**
     * @return iterable<string, array{string, list<string>, list<string>}>
     */
    public static function originSelections(): iterable
    {
        yield 'one origin' => [
            'Weimar Sync Channel',
            ['Event from Weimar'],
            ['Event from Erfurt', 'Event without origin'],
        ];
        yield 'two origins' => [
            'Weimar Sync Channel,Erfurt Sync Channel',
            ['Event from Weimar', 'Event from Erfurt'],
            ['Event without origin'],
        ];
        yield 'no origin' => [
            '',
            ['Event from Weimar', 'Event from Erfurt', 'Event without origin'],
            [],
        ];
    }

    /**
     * @param list<string> $shownTitles
     * @param list<string> $hiddenTitles
     */
    #[Test]
    #[DataProvider('originSelections')]
    public function canFilterEventsByOrigin(string $origins, array $shownTitles, array $hiddenTitles): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/FilterEventsByOrigin.php');

        $request = new InternalRequest('https://example.com/');
        $request = $request->withPageId(1);
        $request = $request->withInstructions([
            $this->getTypoScriptInstruction()->withTypoScript([
                'plugin.' => ['tx_events.' => ['settings.' => ['origins' => $origins]]],
            ]),
        ]);
        $response = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

        foreach ($shownTitles as $title) {
            self::assertStringContainsString('>' . $title . '</a>', $html);
        }
        foreach ($hiddenTitles as $title) {
            self::assertStringNotContainsString('>' . $title . '</a>', $html);
        }
    }

    #[Test]
    public function canFilterDatesByOriginOfTheirEvent(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/FilterDatesByOrigin.php');

        $request = new InternalRequest('https://example.com/');
        $request = $request->withPageId(1);
        $request = $request->withInstructions([
            $this->getTypoScriptInstruction()->withTypoScript([
                'plugin.' => ['tx_events.' => ['settings.' => ['origins' => 'Weimar Sync Channel']]],
            ]),
        ]);
        $response = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

        self::assertStringContainsString('Event from Weimar', $html);
        self::assertStringNotContainsString('Event from Erfurt', $html);
    }
}
