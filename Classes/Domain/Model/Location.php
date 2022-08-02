<?php

namespace Wrm\Events\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Location extends AbstractEntity
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $street = '';

    /**
     * @var string
     */
    protected $zip = '';

    /**
     * @var string
     */
    protected $city = '';

    /**
     * @var string
     */
    protected $district = '';

    /**
     * @var string
     */
    protected $country = '';

    /**
     * @var string
     */
    protected $phone = '';

    /**
     * @var string
     */
    protected $latitude = '';

    /**
     * @var string
     */
    protected $longitude = '';

    /**
     * @var string
     */
    protected $globalId = '';

    /**
     * @var string
     */
    protected $slug = '';

    public function __construct(
        string $name,
        string $street,
        string $zip,
        string $city,
        string $district,
        string $country,
        string $phone,
        string $latitude,
        string $longitude,
        string $slug,
        int $languageUid
    ) {
        $this->name = $name;
        $this->street = $street;
        $this->zip = $zip;
        $this->city = $city;
        $this->district = $district;
        $this->country = $country;
        $this->phone = $phone;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->slug = $slug;
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
            || $this->phone !== ''
            ;
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
            $this->latitude,
            $this->longitude,
        ]));
    }
}
