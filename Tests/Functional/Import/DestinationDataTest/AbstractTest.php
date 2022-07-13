<?php

namespace Wrm\Events\Tests\Functional\Import\DestinationDataTest;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\DateTimeAspect;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Wrm\Events\Command\DestinationDataImportCommand;
use Wrm\Events\Tests\ClientFactory;

abstract class AbstractTest extends FunctionalTestCase
{
    protected $coreExtensionsToLoad = [
        'filelist',
    ];

    protected $testExtensionsToLoad = [
        'typo3conf/ext/events',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importDataSet('EXT:events/Tests/Functional/Import/DestinationDataTest/Fixtures/Structure.xml');
        $this->setUpBackendUserFromFixture(1);

        $languageServiceFactory = $this->getContainer()->get(LanguageServiceFactory::class);
        if (!$languageServiceFactory instanceof LanguageServiceFactory) {
            throw new \UnexpectedValueException('Did not retrieve LanguageServiceFactory.', 1637847250);
        }
        $GLOBALS['LANG'] = $languageServiceFactory->create('default');
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['LANG']);

        parent::tearDown();
    }

    protected function setUpConfiguration(array $configuration): void
    {
        $this->setUpFrontendRootPage(1, [], [
            'config' => implode(PHP_EOL, [
                'module.tx_events_pi1.settings.destinationData {',
                implode(PHP_EOL, $configuration),
                '}',
            ]),
        ]);
    }

    protected function &setUpResponses(array $responses): array
    {
        $requests = [];

        $client = ClientFactory::createClientWithHistory($responses, $requests);
        $container = $this->getContainer();
        if ($container instanceof Container) {
            $container->set(ClientInterface::class, $client);
            // For TYPO3 10 support
            $container->set(GuzzleClientInterface::class, $client);
        }

        return $requests;
    }

    protected function executeCommand(
        array $argumentsAndOptions,
        string $command = DestinationDataImportCommand::class
    ): CommandTester {
        $subject = $this->getContainer()->get($command);
        self::assertInstanceOf(Command::class, $subject);

        $tester = new CommandTester($subject);
        $tester->execute(
            $argumentsAndOptions,
            [
                'capture_stderr_separately' => true,
            ]
        );

        return $tester;
    }

    /**
     * @api Actual tests can use this method to define the actual date of "now".
     */
    protected function setDateAspect(\DateTimeImmutable $dateTime): void
    {
        $context = $this->getContainer()->get(Context::class);
        if (!$context instanceof Context) {
            throw new \TypeError('Retrieved context was of unexpected type.', 1638182021);
        }

        $aspect = new DateTimeAspect($dateTime);
        $context->setAspect('date', $aspect);
    }
}
