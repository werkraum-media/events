<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Frontend;

use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use WerkraumMedia\Events\Tests\Functional\AbstractFunctionalTestCase;

/**
 * @covers \WerkraumMedia\Events\Controller\DateController
 * @covers \WerkraumMedia\Events\Domain\Repository\DateRepository
 */
class FilterTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/SiteStructure.php');
        $this->setUpFrontendRendering();
    }

    /**
     * @test
     */
    public function canFilterByASingleLocationViaFlexform(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/FilterByASingleLocationViaFlexform.php');

        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $response = $this->executeFrontendRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

        self::assertStringNotContainsString('Lotte in Weimar', $html);
        self::assertStringContainsString('Was hat das Universum mit mir zu tun?', $html);
    }

    /**
     * @test
     */
    public function canFilterByTwoLocationsViaFlexform(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/FilterByTwoLocationsViaFlexform.php');

        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $response = $this->executeFrontendRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string)$response->getBody();

        self::assertStringContainsString('Lotte in Weimar', $html);
        self::assertStringContainsString('Was hat das Universum mit mir zu tun?', $html);
    }
}
