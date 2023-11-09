<?php

namespace WerkraumMedia\Events\Tests\Functional\Cleanup;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WerkraumMedia\Events\Command\RemovePastCommand;
use WerkraumMedia\Events\Tests\Functional\AbstractFunctionalTestCase;

/**
 * @testdox Cleanup RemovePast
 */
class RemovePastTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        $this->pathsToProvideInTestInstance = [
            'typo3conf/ext/events/Tests/Functional/Cleanup/Fixtures/RemovePastTestFileadmin/' => 'fileadmin/',
        ];

        parent::setUp();
    }

    /**
     * @test
     */
    public function removesPastData(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/RemovePastTestDatabase.php');

        $subject = $this->getContainer()->get(RemovePastCommand::class);
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
            2,
            $this->getAllRecords('tx_events_domain_model_organizer'),
            'Organizers are not kept.'
        );

        self::assertCount(
            1,
            $this->getAllRecords('tx_events_domain_model_event'),
            'Events are still there.'
        );
        self::assertCount(
            1,
            $this->getAllRecords('tx_events_domain_model_date'),
            'Dates are still there.'
        );

        self::assertCount(
            1,
            $this->getAllRecords('sys_category_record_mm'),
            'Relations to categories still exist.'
        );

        self::assertCount(
            4,
            $this->getAllRecords('sys_file'),
            'Unexpected number of sys_file records.'
        );
        self::assertCount(
            4,
            $this->getAllRecords('sys_file_reference'),
            'Unexpected number of sys_file_reference records.'
        );
        self::assertCount(
            4,
            $this->getAllRecords('sys_file_metadata'),
            'Unexpected number of sys_file_metadata records.'
        );

        $files = GeneralUtility::getFilesInDir('fileadmin/user_uploads');
        self::assertIsArray($files, 'Failed to retrieve files from filesystem.');
        self::assertCount(4, $files, 'Unexpected number of files in filesystem.');
        self::assertSame('example-events-image-used-somewhere-else.gif', array_values($files)[0], 'Unexpected file in filesystem.');
        self::assertSame('example-for-future-event.gif', array_values($files)[1], 'Unexpected file in filesystem.');
        self::assertSame('example-for-partner.gif', array_values($files)[2], 'Unexpected file in filesystem.');
        self::assertSame('example-to-keep.gif', array_values($files)[3], 'Unexpected file in filesystem.');
    }
}
