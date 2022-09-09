<?php

declare(strict_types=1);

namespace Wrm\Events\Tests\Functional\Frontend;

use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \Wrm\Events\Controller\DateController
 * @covers \Wrm\Events\Domain\Repository\DateRepository
 */
class FilterTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/events',
    ];

    protected $coreExtensionsToLoad = [
        'fluid_styled_content',
    ];

    protected $pathsToProvideInTestInstance = [
        'typo3conf/ext/events/Tests/Functional/Frontend/Fixtures/Sites/' => 'typo3conf/sites',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/Fixtures/Database/SiteStructure.csv');
        $this->setUpFrontendRootPage(1, [
            'constants' => [
                'EXT:events/Configuration/TypoScript/constants.typoscript',
            ],
            'setup' => [
                'EXT:fluid_styled_content/Configuration/TypoScript/setup.typoscript',
                'EXT:events/Configuration/TypoScript/setup.typoscript',
                'EXT:events/Tests/Functional/Frontend/Fixtures/TypoScript/Rendering.typoscript'
            ],
        ]);
    }

    /**
     * @test
     */
    public function canFilterByASingleLocationViaFlexform(): void
    {
        $this->importDataSet('EXT:events/Tests/Functional/Frontend/Fixtures/Database/FilterByASingleLocationViaFlexform.xml');

        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $response = $this->executeFrontendRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string) $response->getBody();

        self::assertStringNotContainsString('Lotte in Weimar', $html);
        self::assertStringContainsString('Was hat das Universum mit mir zu tun?', $html);
    }

    /**
     * @test
     */
    public function canFilterByTwoLocationsViaFlexform(): void
    {
        $this->importDataSet('EXT:events/Tests/Functional/Frontend/Fixtures/Database/FilterByTwoLocationsViaFlexform.xml');

        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $response = $this->executeFrontendRequest($request);

        self::assertSame(200, $response->getStatusCode());
        $html = (string) $response->getBody();

        self::assertStringContainsString('Lotte in Weimar', $html);
        self::assertStringContainsString('Was hat das Universum mit mir zu tun?', $html);
    }
}
