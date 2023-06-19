<?php

namespace Wrm\Events\Tests\Functional\Cleanup;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Wrm\Events\Command\RemoveAllCommand;
use Wrm\Events\Tests\Functional\AbstractFunctionalTestCase;

/**
 * @testdox Cleanup RemoveAll
 */
class RemoveAllTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        $this->pathsToProvideInTestInstance = [
            'typo3conf/ext/events/Tests/Functional/Cleanup/Fixtures/RemoveAllTestFileadmin/' => 'fileadmin/',
        ];

        parent::setUp();
    }

    /**
     * @test
     */
    public function removesAllData(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/RemoveAllTestDatabase.php');

        $subject = $this->getContainer()->get(RemoveAllCommand::class);
        self::assertInstanceOf(Command::class, $subject);

        $tester = new CommandTester($subject);
        $tester->execute([], ['capture_stderr_separately' => true]);

        self::assertSame(0, $tester->getStatusCode());
        self::assertCount(
            1,
            $this->getAllRecords('tx_events_domain_model_partner'),
            'Partners are not kept.'
        );
        self::assertCount(
            1,
            $this->getAllRecords('tx_events_domain_model_region'),
            'Regions are not kept.'
        );
        self::assertCount(
            1,
            $this->getAllRecords('tx_events_domain_model_partner'),
            'Partners are not kept.'
        );

        self::assertCount(
            0,
            $this->getAllRecords('tx_events_domain_model_organizer'),
            'Organizers are still there.'
        );
        self::assertCount(
            0,
            $this->getAllRecords('tx_events_domain_model_event'),
            'Events are still there.'
        );
        self::assertCount(
            0,
            $this->getAllRecords('tx_events_domain_model_date'),
            'Dates are still there.'
        );

        self::assertCount(
            0,
            $this->getAllRecords('sys_category_record_mm'),
            'Relations to categories still exist.'
        );

        self::assertCount(
            1,
            $this->getAllRecords('sys_file'),
            'Unexpected number of sys_file records.'
        );
        self::assertCount(
            1,
            $this->getAllRecords('sys_file_reference'),
            'Unexpected number of sys_file_reference records.'
        );
        self::assertCount(
            1,
            $this->getAllRecords('sys_file_metadata'),
            'Unexpected number of sys_file_metadata records.'
        );

        $files = GeneralUtility::getFilesInDir('fileadmin/user_uploads');
        self::assertIsArray($files, 'Failed to retrieve files from filesystem.');
        self::assertCount(1, $files, 'Unexpectd number of files in filesystem.');
        self::assertSame('example-for-partner.gif', array_values($files)[0], 'Unexpectd file in filesystem.');
    }
}
