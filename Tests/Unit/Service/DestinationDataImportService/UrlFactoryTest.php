<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Unit\Service\DestinationDataImportService;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use WerkraumMedia\Events\Domain\Model\Import;
use WerkraumMedia\Events\Service\DestinationDataImportService\UrlFactory;

class UrlFactoryTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $configurationManager = self::createStub(BackendConfigurationManager::class);
        $configurationManager->method('getConfiguration')->willReturn([]);

        $subject = new UrlFactory(
            $configurationManager
        );

        self::assertInstanceOf(
            UrlFactory::class,
            $subject
        );
    }

    #[DataProvider('possibleImports')]
    #[Test]
    public function createSearchResultUrl(
        Stub $import,
        array $settings,
        string $expectedResult
    ): void {
        $configurationManager = self::createStub(BackendConfigurationManager::class);
        $configurationManager->method('getConfiguration')->willReturn(['settings' => ['destinationData' => $settings]]);

        $subject = new UrlFactory(
            $configurationManager
        );

        $result = $subject->createSearchResultUrl($import);

        self::assertSame(
            $result,
            $expectedResult
        );
    }

    public static function possibleImports(): array
    {
        return [
            'All provided' => [
                'import' => (function () {
                    $import = self::createStub(Import::class);
                    $import->method('getRestExperience')->willReturn('experience');
                    $import->method('getSearchQuery')->willReturn('');

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
                    $import = self::createStub(Import::class);
                    $import->method('getRestExperience')->willReturn('');
                    $import->method('getSearchQuery')->willReturn('');

                    return $import;
                })(),
                'settings' => [
                    'restUrl' => 'https://example.com/path',
                ],
                'expectedResult' => 'https://example.com/path',
            ],
            'Some missing' => [
                'import' => (function () {
                    $import = self::createStub(Import::class);
                    $import->method('getRestExperience')->willReturn('experience');
                    $import->method('getSearchQuery')->willReturn('');

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
                    $import = self::createStub(Import::class);
                    $import->method('getRestExperience')->willReturn('experience');
                    $import->method('getSearchQuery')->willReturn('name:"Test Something"');

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
