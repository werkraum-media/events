<?php
namespace Wrm\Events\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Dirk Koritnik <koritnik@werkraum-media.de>
 */
class OrganizerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Wrm\Events\Domain\Model\Organizer
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Wrm\Events\Domain\Model\Organizer();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameForStringSetsName()
    {
        $this->subject->setName('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'name',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getStreetReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getStreet()
        );
    }

    /**
     * @test
     */
    public function setStreetForStringSetsStreet()
    {
        $this->subject->setStreet('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'street',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDistrictReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getDistrict()
        );
    }

    /**
     * @test
     */
    public function setDistrictForStringSetsDistrict()
    {
        $this->subject->setDistrict('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'district',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getCityReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getCity()
        );
    }

    /**
     * @test
     */
    public function setCityForStringSetsCity()
    {
        $this->subject->setCity('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'city',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getZipReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getZip()
        );
    }

    /**
     * @test
     */
    public function setZipForStringSetsZip()
    {
        $this->subject->setZip('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'zip',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getPhoneReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getPhone()
        );
    }

    /**
     * @test
     */
    public function setPhoneForStringSetsPhone()
    {
        $this->subject->setPhone('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'phone',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getWebReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getWeb()
        );
    }

    /**
     * @test
     */
    public function setWebForStringSetsWeb()
    {
        $this->subject->setWeb('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'web',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getEmailReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getEmail()
        );
    }

    /**
     * @test
     */
    public function setEmailForStringSetsEmail()
    {
        $this->subject->setEmail('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'email',
            $this->subject
        );
    }
}
