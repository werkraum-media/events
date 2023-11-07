<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Location extends AbstractEntity
{
    protected string $globalId = '';

    public function __construct(
        protected string $name,
        protected string $street,
        protected string $zip,
        protected string $city,
        protected string $district,
        protected string $country,
        protected string $phone,
        protected string $latitude,
        protected string $longitude,
        int $languageUid
    ) {
        $this->latitude = $this->normalizeGeocoordinate($latitude);
        $this->longitude = $this->normalizeGeocoordinate($longitude);
        $this->_languageUid = $languageUid;

        $this->globalId = $this->generateGlobalId();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getDistrict(): string
    {
        return $this->district;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getLatitude(): string
    {
        return $this->latitude;
    }

    public function getLongitude(): string
    {
        return $this->longitude;
    }

    public function getGlobalId(): string
    {
        return $this->globalId;
    }

    public function updateFromLocation(self $location): void
    {
        // Only updates values not being part of global id.
        $this->phone = $location->getPhone();
        $this->longitude = $location->getLongitude();
        $this->latitude = $location->getLatitude();
    }

    /**
     * Validates the location.
     *
     * Holds the original logic that at least one property must be given.
     */
    public function isValid(): bool
    {
        return $this->name !== ''
            || $this->street !== ''
            || $this->zip !== ''
            || $this->city !== ''
            || $this->district !== ''
            || $this->country !== ''
            || $this->phone !== '';
    }

    private function generateGlobalId(): string
    {
        return hash('sha256', implode(',', [
            $this->name,
            $this->street,
            $this->zip,
            $this->city,
            $this->district,
            $this->country,
        ]));
    }

    private function normalizeGeocoordinate(string $coordinate): string
    {
        $numberOfCommas = substr_count($coordinate, ',');
        $numberOfPoints = substr_count($coordinate, '.');

        if (
            $numberOfCommas === 1
            && $numberOfPoints === 0
        ) {
            $coordinate = str_replace(',', '.', $coordinate);
        }

        return number_format((float)$coordinate, 6, '.', '');
    }
}
