<?php

namespace Wrm\Events\Tests\Functional\Import\DestinationDataTest;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Wrm\Events\Command\DestinationDataImportCommand;
use Wrm\Events\Tests\ClientFactory;

/**
 * @testdox DestinationData import
 */
class ImportsWithoutRegionIfNotProvided extends AbstractTest
{
    /**
     * @test
     */
    public function importsWithoutRegionIfNotProvided(): void
    {
        $fileImportPath = 'staedte/beispielstadt/events/';
        $this->setUpFrontendRootPage(1, [], [
            'config' => implode(PHP_EOL, [
                'module.tx_events_pi1.settings.destinationData {',
                'restUrl = ' . $this->getInstancePath() . '/typo3conf/ext/events/Tests/Functional/Import/DestinationDataTest/Fixtures/Response.json',
                'license = example-license',
                'restType = Event',
                'restLimit = 3',
                'restMode = next_months,12',
                'restTemplate = ET2014A.json',
                'categoriesPid = ',
                'categoryParentUid = ',
                '}',
            ]),
        ]);

        $requests = [];
        $client = ClientFactory::createClientWithHistory([
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
            new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/ExampleImage.jpg') ?: ''),
        ], $requests);
        $container = $this->getContainer();
        if ($container instanceof Container) {
            $container->set(ClientInterface::class, $client);
            // For TYPO3 10 support
            $container->set(GuzzleClientInterface::class, $client);
        }

        $subject = $this->getContainer()->get(DestinationDataImportCommand::class);
        self::assertInstanceOf(Command::class, $subject);

        $tester = new CommandTester($subject);
        $tester->execute(
            [
                'storage-pid' => 2,
                'rest-experience' => 'beispielstadt',
                'files-folder' => $fileImportPath,
            ],
            [
                'capture_stderr_separately' => true,
            ]
        );

        self::assertSame(0, $tester->getStatusCode());

        self::assertCount(3, $requests, 'Unexpected number of requests were made.');
        self::assertSame('https://dam.destination.one/849917/279ac45b3fc701a7197131f627164fffd9f8cc77bc75165e2fc2b864ed606920/theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg', (string)$requests[0]['request']->getUri());
        self::assertSame('https://dam.destination.one/828118/f13bbf5602ffc406ebae2faa3527654dea84194666bce4925a1ca8bd3f50c5e9/tueftlerzeit-sfz-rudolstadt-jpg.jpg', (string)$requests[1]['request']->getUri());
        self::assertSame('https://dam.destination.one/853436/109ac1cf87913e21b5e2b0ef0cc63d223a14374364952a855746a8e7c3fcfc36/lutherkirche-jpg.jpg', (string)$requests[2]['request']->getUri());

        self::assertCount(
            0,
            $this->getAllRecords('tx_events_domain_model_partner'),
            'Added unexpected partners.'
        );
        self::assertCount(
            0,
            $this->getAllRecords('tx_events_domain_model_region'),
            'Added unexpected region.'
        );
        $this->assertCSVDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Assertions/ImportsWithoutRegionIfNotProvided.csv');


        $importedFiles = GeneralUtility::getFilesInDir($this->getInstancePath() . '/fileadmin/' . $fileImportPath);
        self::assertIsArray($importedFiles, 'Failed to retrieve imported files from filesystem.');
        self::assertSame(
            [
                'lutherkirche-jpg.jpg',
                'theater-rudolstadt_johannes-gei-er_photo-by-lisa-stern_web_-jpg.jpg',
                'tueftlerzeit-sfz-rudolstadt-jpg.jpg',
            ],
            array_values($importedFiles),
            'Got unexpected number of files'
        );
    }
}
