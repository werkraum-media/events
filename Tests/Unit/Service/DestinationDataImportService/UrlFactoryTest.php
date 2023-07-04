<?php

namespace Wrm\Events\Tests\Unit\Service\DestinationDataImportService;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use Wrm\Events\Domain\Model\Import;
use Wrm\Events\Service\DestinationDataImportService\UrlFactory;
use Wrm\Events\Tests\ProphecyTrait;

/**
 * @covers \Wrm\Events\Service\DestinationDataImportService\UrlFactory
 */
class UrlFactoryTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function canBeCreated(): void
    {
        $configurationManager = $this->prophesize(ConfigurationManager::class);
        $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'Events',
            'Pi1'
        )->willReturn([]);

        $subject = new UrlFactory(
            $configurationManager->reveal()
        );

        self::assertInstanceOf(
            UrlFactory::class,
            $subject
        );
    }

    /**
     * @test
     *
     * @dataProvider possibleImports
     */
    public function createSearchResultUrl(
        ObjectProphecy $import,
        array $settings,
        string $expectedResult
    ): void {
        $configurationManager = $this->prophesize(ConfigurationManager::class);
        $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'Events',
            'Pi1'
        )->willReturn(['destinationData' => $settings]);

        $subject = new UrlFactory(
            $configurationManager->reveal()
        );

        $result = $subject->createSearchResultUrl($import->reveal());

        self::assertSame(
            $result,
            $expectedResult
        );
    }

    public function possibleImports(): array
    {
        return [
            'All provided' => [
                'import' => (function () {
                    $import = $this->prophesize(Import::class);
                    $import->getRestExperience()->willReturn('experience');
                    $import->getSearchQuery()->willReturn('');

                    return $import;
                })(),
                'settings' => [
                    'restUrl' => 'https://example.com/path',
                    'license' => 'licenseKey',
                    'restType' => 'restType',
                    'restMode' => 'restMode',
                    'restLimit' => 'restLimit',
                    'restTemplate' => 'restTemplate',
                ],
                'expectedResult' => 'https://example.com/path?experience=experience&licensekey=licenseKey&type=restType&mode=restMode&limit=restLimit&template=restTemplate',
            ],
            'All missing' => [
                'import' => (function () {
                    $import = $this->prophesize(Import::class);
                    $import->getRestExperience()->willReturn('');
                    $import->getSearchQuery()->willReturn('');

                    return $import;
                })(),
                'settings' => [
                    'restUrl' => 'https://example.com/path',
                ],
                'expectedResult' => 'https://example.com/path',
            ],
            'Some missing' => [
                'import' => (function () {
                    $import = $this->prophesize(Import::class);
                    $import->getRestExperience()->willReturn('experience');
                    $import->getSearchQuery()->willReturn('');

                    return $import;
                })(),
                'settings' => [
                    'restUrl' => 'https://example.com/path',
                    'license' => 'licenseKey',
                    'restLimit' => 'restLimit',
                    'restTemplate' => 'restTemplate',
                ],
                'expectedResult' => 'https://example.com/path?experience=experience&licensekey=licenseKey&limit=restLimit&template=restTemplate',
            ],
            'With search query' => [
                'import' => (function () {
                    $import = $this->prophesize(Import::class);
                    $import->getRestExperience()->willReturn('experience');
                    $import->getSearchQuery()->willReturn('name:"Test Something"');

                    return $import;
                })(),
                'settings' => [
                    'restUrl' => 'https://example.com/path',
                ],
                'expectedResult' => 'https://example.com/path?experience=experience&q=name%3A%22Test+Something%22',
            ],
        ];
    }
}
