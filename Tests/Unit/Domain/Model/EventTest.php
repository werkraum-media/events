<?php
namespace Wrm\Events\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Dirk Koritnik <koritnik@werkraum-media.de>
 */
class EventTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Wrm\Events\Domain\Model\Event
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Wrm\Events\Domain\Model\Event();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getTitleReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle()
    {
        $this->subject->setTitle('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'title',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getGlobalIdReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getGlobalId()
        );
    }

    /**
     * @test
     */
    public function setGlobalIdForStringSetsGlobalId()
    {
        $this->subject->setGlobalId('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'globalId',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSlugReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getSlug()
        );
    }

    /**
     * @test
     */
    public function setSlugForStringSetsSlug()
    {
        $this->subject->setSlug('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'slug',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getHighlightReturnsInitialValueForBool()
    {
        self::assertSame(
            false,
            $this->subject->getHighlight()
        );
    }

    /**
     * @test
     */
    public function setHighlightForBoolSetsHighlight()
    {
        $this->subject->setHighlight(true);

        self::assertAttributeEquals(
            true,
            'highlight',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTeaserReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getTeaser()
        );
    }

    /**
     * @test
     */
    public function setTeaserForStringSetsTeaser()
    {
        $this->subject->setTeaser('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'teaser',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDetailsReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getDetails()
        );
    }

    /**
     * @test
     */
    public function setDetailsForStringSetsDetails()
    {
        $this->subject->setDetails('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'details',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getPriceInfoReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getPriceInfo()
        );
    }

    /**
     * @test
     */
    public function setPriceInfoForStringSetsPriceInfo()
    {
        $this->subject->setPriceInfo('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'priceInfo',
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
    public function getCountryReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getCountry()
        );
    }

    /**
     * @test
     */
    public function setCountryForStringSetsCountry()
    {
        $this->subject->setCountry('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'country',
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
    public function getBookingReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getBooking()
        );
    }

    /**
     * @test
     */
    public function setBookingForStringSetsBooking()
    {
        $this->subject->setBooking('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'booking',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTicketReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getTicket()
        );
    }

    /**
     * @test
     */
    public function setTicketForStringSetsTicket()
    {
        $this->subject->setTicket('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'ticket',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getFacebookReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getFacebook()
        );
    }

    /**
     * @test
     */
    public function setFacebookForStringSetsFacebook()
    {
        $this->subject->setFacebook('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'facebook',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getYoutubeReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getYoutube()
        );
    }

    /**
     * @test
     */
    public function setYoutubeForStringSetsYoutube()
    {
        $this->subject->setYoutube('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'youtube',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getLatitudeReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getLatitude()
        );
    }

    /**
     * @test
     */
    public function setLatitudeForStringSetsLatitude()
    {
        $this->subject->setLatitude('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'latitude',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getLongitudeReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getLongitude()
        );
    }

    /**
     * @test
     */
    public function setLongitudeForStringSetsLongitude()
    {
        $this->subject->setLongitude('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'longitude',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getImagesReturnsInitialValueForFileReference()
    {
        self::assertEquals(
            null,
            $this->subject->getImages()
        );
    }

    /**
     * @test
     */
    public function setImagesForFileReferenceSetsImages()
    {
        $fileReferenceFixture = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $this->subject->setImages($fileReferenceFixture);

        self::assertAttributeEquals(
            $fileReferenceFixture,
            'images',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getCategoriesReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getCategories()
        );
    }

    /**
     * @test
     */
    public function setCategoriesForIntSetsCategories()
    {
        $this->subject->setCategories(12);

        self::assertAttributeEquals(
            12,
            'categories',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDatesReturnsInitialValueForDate()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getDates()
        );
    }

    /**
     * @test
     */
    public function setDatesForObjectStorageContainingDateSetsDates()
    {
        $date = new \Wrm\Events\Domain\Model\Date();
        $objectStorageHoldingExactlyOneDates = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneDates->attach($date);
        $this->subject->setDates($objectStorageHoldingExactlyOneDates);

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneDates,
            'dates',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addDateToObjectStorageHoldingDates()
    {
        $date = new \Wrm\Events\Domain\Model\Date();
        $datesObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $datesObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($date));
        $this->inject($this->subject, 'dates', $datesObjectStorageMock);

        $this->subject->addDate($date);
    }

    /**
     * @test
     */
    public function removeDateFromObjectStorageHoldingDates()
    {
        $date = new \Wrm\Events\Domain\Model\Date();
        $datesObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $datesObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($date));
        $this->inject($this->subject, 'dates', $datesObjectStorageMock);

        $this->subject->removeDate($date);
    }

    /**
     * @test
     */
    public function getOrganizerReturnsInitialValueForOrganizer()
    {
        self::assertEquals(
            null,
            $this->subject->getOrganizer()
        );
    }

    /**
     * @test
     */
    public function setOrganizerForOrganizerSetsOrganizer()
    {
        $organizerFixture = new \Wrm\Events\Domain\Model\Organizer();
        $this->subject->setOrganizer($organizerFixture);

        self::assertAttributeEquals(
            $organizerFixture,
            'organizer',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getRegionReturnsInitialValueForRegion()
    {
        self::assertEquals(
            null,
            $this->subject->getRegion()
        );
    }

    /**
     * @test
     */
    public function setRegionForRegionSetsRegion()
    {
        $regionFixture = new \Wrm\Events\Domain\Model\Region();
        $this->subject->setRegion($regionFixture);

        self::assertAttributeEquals(
            $regionFixture,
            'region',
            $this->subject
        );
    }
}
