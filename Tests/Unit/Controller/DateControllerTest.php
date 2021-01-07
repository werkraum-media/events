<?php

namespace Wrm\Events\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author Dirk Koritnik <koritnik@werkraum-media.de>
 */
class DateControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Wrm\Events\Controller\DateController
     */
    protected $subject = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Wrm\Events\Controller\DateController::class)
            ->setMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     */
    public function listActionFetchesAllDatesFromRepositoryAndAssignsThemToView()
    {

        $allDates = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        // $dateRepository = $this->getMockBuilder(\::class)
        //     ->setMethods(['findAll'])
        //     ->disableOriginalConstructor()
        //     ->getMock();
        $dateRepository->expects(self::once())->method('findAll')->will(self::returnValue($allDates));
        $this->inject($this->subject, 'dateRepository', $dateRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('dates', $allDates);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenDateToView()
    {
        $date = new \Wrm\Events\Domain\Model\Date();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('date', $date);

        $this->subject->showAction($date);
    }
}
