<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Import\DestinationDataTest;

use Symfony\Component\DependencyInjection\Container;
use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;
use WerkraumMedia\Events\Tests\Functional\AbstractFunctionalTestCase;

abstract class AbstractTestCase extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        $this->coreExtensionsToLoad[] = 'filemetadata';

        parent::setUp();

        // Empty log file for upcoming tests.
        file_put_contents($this->getLogFile(), '');

        $this->importPHPDataSet(__DIR__ . '/Fixtures/Database/Structure.php');
    }

    protected function assertEmptyLog(): void
    {
        self::assertFileEquals(
            __DIR__ . '/Assertions/EmptyLogFile.txt',
            $this->getLogFile(),
            'Logfile was not empty.'
        );
    }

    protected function assertMessageInLog(string $message): void
    {
        self::assertStringContainsString(
            $message,
            file_get_contents($this->getLogFile()),
            'Logfile did not contain the message.'
        );
    }

    protected function addEventListener(string $event, object $listener): void
    {
        $identifier = (string)spl_object_id($listener);

        $container = $this->getContainer();
        assert($container instanceof Container);

        $container->set($identifier, $listener);
        $this->get(ListenerProvider::class)->addListener($event, $identifier);
    }

    private function getLogFile(): string
    {
        return $this->getInstancePath() . '/typo3temp/var/log/typo3_0493d91d8e.log';
    }
}
