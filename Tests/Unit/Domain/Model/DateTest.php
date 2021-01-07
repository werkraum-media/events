<?php

namespace Wrm\Events\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Dirk Koritnik <koritnik@werkraum-media.de>
 */
class DateTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Wrm\Events\Domain\Model\Date
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Wrm\Events\Domain\Model\Date();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getStartReturnsInitialValueForDateTime()
    {
        self::assertEquals(
            null,
            $this->subject->getStart()
        );
    }

    /**
     * @test
     */
    public function setStartForDateTimeSetsStart()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setStart($dateTimeFixture);

        self::assertAttributeEquals(
            $dateTimeFixture,
            'start',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getEndReturnsInitialValueForDateTime()
    {
        self::assertEquals(
            null,
            $this->subject->getEnd()
        );
    }

    /**
     * @test
     */
    public function setEndForDateTimeSetsEnd()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setEnd($dateTimeFixture);

        self::assertAttributeEquals(
            $dateTimeFixture,
            'end',
            $this->subject
        );
    }
}
