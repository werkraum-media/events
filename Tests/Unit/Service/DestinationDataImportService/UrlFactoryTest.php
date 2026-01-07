<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Unit\Service\DestinationDataImportService;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use WerkraumMedia\Events\Domain\Model\Import;
use WerkraumMedia\Events\Service\DestinationDataImportService\UrlFactory;

class UrlFactoryTest extends TestCase
{
    #[Test]
    public function canBeCreated(): void
    {
        $subject = new UrlFactory();

        self::assertInstanceOf(
            UrlFactory::class,
            $subject
        );
    }

    #[DataProvider('possibleImports')]
    #[Test]
    /**
     * @param Stub&Import $import
     */
    public function createSearchResultUrl(
        Import $import,
        string $expectedResult
    ): void {
        $subject = new UrlFactory();

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
                    $import->method('getRestLicenseKey')->willReturn('licenseKey');
                    $import->method('getRestExperience')->willReturn('experience');
                    $import->method('getRestMode')->willReturn('restMode');
                    $import->method('getRestSearchQuery')->willReturn('');

                    return $import;
                })(),
                'expectedResult' => 'http://meta.et4.de/rest.ashx/search/?experience=experience&licensekey=licenseKey&type=Event&mode=restMode&template=ET2014A.json&limit=500&sort=globalid+asc%2C+title+asc',
            ],
            'All missing' => [
                'import' => (function () {
                    return self::createStub(Import::class);
                })(),
                'expectedResult' => 'http://meta.et4.de/rest.ashx/search/?type=Event&template=ET2014A.json&limit=500&sort=globalid+asc%2C+title+asc',
            ],
            'Some missing' => [
                'import' => (function () {
                    $import = self::createStub(Import::class);
                    $import->method('getRestLicenseKey')->willReturn('licenseKey');
                    $import->method('getRestExperience')->willReturn('experience');

                    return $import;
                })(),
                'expectedResult' => 'http://meta.et4.de/rest.ashx/search/?experience=experience&licensekey=licenseKey&type=Event&template=ET2014A.json&limit=500&sort=globalid+asc%2C+title+asc',
            ],
            'With search query' => [
                'import' => (function () {
                    $import = self::createStub(Import::class);
                    $import->method('getRestExperience')->willReturn('experience');
                    $import->method('getRestSearchQuery')->willReturn('name:"Test Something"');

                    return $import;
                })(),
                'expectedResult' => 'http://meta.et4.de/rest.ashx/search/?experience=experience&type=Event&template=ET2014A.json&q=name%3A%22Test+Something%22&limit=500&sort=globalid+asc%2C+title+asc',
            ],
        ];
    }
}
