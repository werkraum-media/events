<?php

declare(strict_types=1);

namespace Wrm\Events\Tests\Unit\Domain\Model\Event;

use PHPUnit\Framework\TestCase;
use Wrm\Events\Domain\Model\Event;
use Wrm\Events\Domain\Model\Location;

/**
 * @covers \Wrm\Events\Domain\Model\Event
 */
class LocationDataTest extends TestCase
{
    /**
     * @test
     */
    public function returnsLocationNameFromLegacyProperty(): void
    {
        $subject = new Event();
        $subject->_setProperty('name', 'Location Name');

        self::assertSame(
            'Location Name',
            $subject->getName()
        );
    }

    /**
     * @test
     */
    public function returnsLocationStreetFromLegacyProperty(): void
    {
        $subject = new Event();
        $subject->_setProperty('street', 'Mußterstraße 24');

        self::assertSame(
            'Mußterstraße 24',
            $subject->getStreet()
        );
    }

    /**
     * @test
     */
    public function returnsLocationDistrictFromLegacyProperty(): void
    {
        $subject = new Event();
        $subject->_setProperty('district', 'NRW');

        self::assertSame(
            'NRW',
            $subject->getDistrict()
        );
    }

    /**
     * @test
     */
    public function returnsLocationCityFromLegacyProperty(): void
    {
        $subject = new Event();
        $subject->_setProperty('city', 'Weimar');

        self::assertSame(
            'Weimar',
            $subject->getCity()
        );
    }

    /**
     * @test
     */
    public function returnsLocationZipFromLegacyProperty(): void
    {
        $subject = new Event();
        $subject->_setProperty('zip', '41367');

        self::assertSame(
            '41367',
            $subject->getZip()
        );
    }

    /**
     * @test
     */
    public function returnsLocationCountryFromLegacyProperty(): void
    {
        $subject = new Event();
        $subject->_setProperty('country', 'Germany');

        self::assertSame(
            'Germany',
            $subject->getCountry()
        );
    }

    /**
     * @test
     */
    public function returnsLocationPhoneFromLegacyProperty(): void
    {
        $subject = new Event();
        $subject->_setProperty('phone', '+49 2161 333 333 333');

        self::assertSame(
            '+49 2161 333 333 333',
            $subject->getPhone()
        );
    }

    /**
     * @test
     */
    public function returnsLocationLatitudeFromLegacyProperty(): void
    {
        $subject = new Event();
        $subject->_setProperty('latitude', '50.720971023259');

        self::assertSame(
            '50.720971023259',
            $subject->getLatitude()
        );
    }

    /**
     * @test
     */
    public function returnsLocationLongitudeFromLegacyProperty(): void
    {
        $subject = new Event();
        $subject->_setProperty('longitude', '11.335229873657');

        self::assertSame(
            '11.335229873657',
            $subject->getLongitude()
        );
    }

    /**
     * @test
     */
    public function returnsLocationNameFromLocation(): void
    {
        $subject = new Event();
        $subject->setLocation(new Location(
            'Location Name',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            -1
        ));

        self::assertSame(
            'Location Name',
            $subject->getName()
        );
    }

    /**
     * @test
     */
    public function returnsLocationStreetFromLocation(): void
    {
        $subject = new Event();
        $subject->setLocation(new Location(
            '',
            'Mußterstraße 24',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            -1
        ));

        self::assertSame(
            'Mußterstraße 24',
            $subject->getStreet()
        );
    }

    /**
     * @test
     */
    public function returnsLocationDistrictFromLocation(): void
    {
        $subject = new Event();
        $subject->setLocation(new Location(
            '',
            '',
            '',
            '',
            'NRW',
            '',
            '',
            '',
            '',
            -1
        ));

        self::assertSame(
            'NRW',
            $subject->getDistrict()
        );
    }

    /**
     * @test
     */
    public function returnsLocationCityFromLocation(): void
    {
        $subject = new Event();
        $subject->setLocation(new Location(
            '',
            '',
            '',
            'Weimar',
            '',
            '',
            '',
            '',
            '',
            -1
        ));

        self::assertSame(
            'Weimar',
            $subject->getCity()
        );
    }

    /**
     * @test
     */
    public function returnsLocationZipFromLocation(): void
    {
        $subject = new Event();
        $subject->setLocation(new Location(
            '',
            '',
            '41367',
            '',
            '',
            '',
            '',
            '',
            '',
            -1
        ));

        self::assertSame(
            '41367',
            $subject->getZip()
        );
    }

    /**
     * @test
     */
    public function returnsLocationCountryFromLocation(): void
    {
        $subject = new Event();
        $subject->setLocation(new Location(
            '',
            '',
            '',
            '',
            '',
            'Germany',
            '',
            '',
            '',
            -1
        ));

        self::assertSame(
            'Germany',
            $subject->getCountry()
        );
    }

    /**
     * @test
     */
    public function returnsLocationPhoneFromLocation(): void
    {
        $subject = new Event();
        $subject->setLocation(new Location(
            '',
            '',
            '',
            '',
            '',
            '',
            '+49 2161 333 333 333',
            '',
            '',
            -1
        ));

        self::assertSame(
            '+49 2161 333 333 333',
            $subject->getPhone()
        );
    }

    /**
     * @test
     */
    public function returnsLocationLatitudeFromLocation(): void
    {
        $subject = new Event();
        $subject->setLocation(new Location(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '50.720971023259',
            '',
            -1
        ));

        self::assertSame(
            '50.720971023259',
            $subject->getLatitude()
        );
    }

    /**
     * @test
     */
    public function returnsLocationLongitudeFromLocation(): void
    {
        $subject = new Event();
        $subject->setLocation(new Location(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '11.335229873657',
            -1
        ));

        self::assertSame(
            '11.335229873657',
            $subject->getLongitude()
        );
    }
}
