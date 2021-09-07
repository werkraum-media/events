<?php

namespace Wrm\Events\Domain\Model;

/*
 *
 * This file is part of the "DD Events" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Dirk Koritnik <koritnik@werkraum-media.de>
 *
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Organizer
 */
class Organizer extends AbstractEntity
{
    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * street
     *
     * @var string
     */
    protected $street = '';

    /**
     * district
     *
     * @var string
     */
    protected $district = '';

    /**
     * city
     *
     * @var string
     */
    protected $city = '';

    /**
     * zip
     *
     * @var string
     */
    protected $zip = '';

    /**
     * phone
     *
     * @var string
     */
    protected $phone = '';

    /**
     * web
     *
     * @var string
     */
    protected $web = '';

    /**
     * email
     *
     * @var string
     */
    protected $email = '';

    /**
     * @var int
     */
    protected $_languageUid;

    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the street
     *
     * @return string $street
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Sets the street
     *
     * @param string $street
     * @return void
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * Returns the district
     *
     * @return string $district
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * Sets the district
     *
     * @param string $district
     * @return void
     */
    public function setDistrict($district)
    {
        $this->district = $district;
    }

    /**
     * Returns the city
     *
     * @return string $city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Sets the city
     *
     * @param string $city
     * @return void
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Returns the zip
     *
     * @return string $zip
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Sets the zip
     *
     * @param string $zip
     * @return void
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * Returns the phone
     *
     * @return string $phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Sets the phone
     *
     * @param string $phone
     * @return void
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Returns the web
     *
     * @return string $web
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * Sets the web
     *
     * @param string $web
     * @return void
     */
    public function setWeb($web)
    {
        $this->web = $web;
    }

    /**
     * Returns the email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email
     *
     * @param string $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * __construct
     */
    public function __construct()
    {

        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
    }

    /**
     * @param int $languageUid
     * @return void
     */
    public function setLanguageUid($languageUid)
    {
        $this->_languageUid = $languageUid;
    }

    /**
     * @return int
     */
    public function getLanguageUid()
    {
        return $this->_languageUid;
    }
}
