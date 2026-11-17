<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Tests\Functional\Backend;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\DataHandling\ItemsProcessorContext;
use TYPO3\CMS\Core\Schema\Struct\SelectItem;
use TYPO3\CMS\Core\Schema\Struct\SelectItemCollection;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use WerkraumMedia\Events\Backend\FormEngine\OriginChoicesItemsProcessor;
use WerkraumMedia\Events\Tests\Functional\AbstractFunctionalTestCase;

final class OriginChoicesItemsProcessorTest extends AbstractFunctionalTestCase
{
    #[Test]
    public function offersEachStoredOriginTitleOfTheTableOnce(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/OriginChoices.php');

        $items = new SelectItemCollection();
        $items->add(new SelectItem(type: 'select', label: 'Existing', value: 'existing'));

        $context = new ItemsProcessorContext(
            table: 'tt_content',
            field: 'settings.origins',
            row: [],
            fieldConfiguration: [],
            processorParameters: ['table' => 'tx_events_domain_model_event'],
            realPid: 1,
            site: new NullSite(),
        );

        $subject = $this->get(OriginChoicesItemsProcessor::class);
        self::assertInstanceOf(OriginChoicesItemsProcessor::class, $subject);

        $choices = [];
        foreach ($subject->processItems($items, $context) as $item) {
            $choices[] = [$item->getLabel(), $item->getValue()];
        }

        self::assertSame([
            ['Existing', 'existing'],
            ['Erfurt Sync Channel', 'Erfurt Sync Channel'],
            ['Weimar Sync Channel', 'Weimar Sync Channel'],
        ], $choices);
    }
}
