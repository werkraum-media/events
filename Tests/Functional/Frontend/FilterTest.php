<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Frontend;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use WerkraumMedia\Events\Tests\Functional\AbstractFunctionalTestCase;

class FilterTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SiteStructure.php');
        $this->setUpFrontendRendering();
    }

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
}
