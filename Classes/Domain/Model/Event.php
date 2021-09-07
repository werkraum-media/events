<?php

namespace Wrm\Events\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use Wrm\Events\Domain\Repository\DateRepository;
use Wrm\Events\Service\DataProcessingForModels;

/**
 * Event
 */
class Event extends AbstractEntity
{
    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * subtitle
     *
     * @var string
     */
    protected $subtitle = '';

    /**
     * globalId
     *
     * @var string
     */
    protected $globalId = '';

    /**
     * slug
     *
     * @var string
     */
    protected $slug = '';

    /**
     * highlight
     *
     * @var bool
     */
    protected $highlight = false;

    /**
     * teaser
     *
     * @var string
     */
    protected $teaser = '';

    /**
     * details
     *
     * @var string
     */
    protected $details = '';

    /**
     * priceInfo
     *
     * @var string
     */
    protected $priceInfo = '';

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
     * country
     *
     * @var string
     */
    protected $country = '';

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
     * ticket
     *
     * @var string
     */
    protected $ticket = '';

    /**
     * facebook
     *
     * @var string
     */
    protected $facebook = '';

    /**
     * youtube
     *
     * @var string
     */
    protected $youtube = '';

    /**
     * instagram
     *
     * @var string
     */
    protected $instagram = '';

    /**
     * latitude
     *
     * @var string
     */
    protected $latitude = '';

    /**
     * longitude
     *
     * @var string
     */
    protected $longitude = '';

    /**
     * images
     *
     * @var ObjectStorage<FileReference>
     * @Extbase\ORM\Cascade remove
     */
    protected $images;

    /**
     * dates
     *
     * @var ObjectStorage<Date>
     * @Extbase\ORM\Cascade remove
     */
    protected $dates;

    /**
     * organizer
     *
     * @var \Wrm\Events\Domain\Model\Organizer
     */
    protected $organizer = null;

    /**
     * region
     *
     * @var Region
     */
    protected $region = null;

    /**
     * @var string
     */
    protected $pages = '';

    /**
     * categories
     *
     * @var ObjectStorage<Category>
     */
    protected $categories;

    /**
     * @var ObjectStorage<Partner>
     */
    protected $partner;

    /**
     * @var ObjectStorage<Event>
     */
    protected $referencesEvents;

    /**
     * @var int
     */
    protected $_languageUid;

    /**
     * @var DataProcessingForModels
     */
    protected $dataProcessing = null;

    public function __construct()
    {
        $this->initStorageObjects();
    }

    /**
     * @param DataProcessingForModels $dataProcessing
     */
    public function injectDataProcessingForModels(DataProcessingForModels $dataProcessing)
    {
        $this->dataProcessing = $dataProcessing;
    }

    public function initializeObject()
    {
        $this->initStorageObjects();
    }

    /**
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->images = new ObjectStorage();
        $this->dates = new ObjectStorage();
        $this->categories = new ObjectStorage();
        $this->partner = new ObjectStorage();
        $this->referencesEvents = new ObjectStorage();
    }

    /**
     * Returns the globalId
     *
     * @return string $globalId
     */
    public function getGlobalId()
    {
        return $this->globalId;
    }

    /**
     * @param string $globalId
     * @return void
     */
    public function setGlobalId($globalId)
    {
        $this->globalId = $globalId;
    }

    /**
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string $subtitle
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param string $subtitle
     * @return void
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }

    /**
     * @return string $teaser
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * @param string $teaser
     * @return void
     */
    public function setTeaser($teaser)
    {
        $this->teaser = $teaser;
    }

    /**
     * @return string $details
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param string $details
     * @return void
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }

    /**
     * @return string $priceInfo
     */
    public function getPriceInfo()
    {
        return $this->priceInfo;
    }

    /**
     * @param string $priceInfo
     * @return void
     */
    public function setPriceInfo($priceInfo)
    {
        $this->priceInfo = $priceInfo;
    }

    /**
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string $street
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $street
     * @return void
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return string $district
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param string $district
     * @return void
     */
    public function setDistrict($district)
    {
        $this->district = $district;
    }

    /**
     * @return string $city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return void
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string $zip
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     * @return void
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string $web
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * @param string $web
     * @return void
     */
    public function setWeb($web)
    {
        $this->web = $web;
    }

    /**
     * @return string $ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @param string $ticket
     * @return void
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * @return string $facebook
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * @param string $facebook
     * @return void
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * @return string $youtube
     */
    public function getYoutube()
    {
        return $this->youtube;
    }

    /**
     * @param string $youtube
     * @return void
     */
    public function setYoutube($youtube)
    {
        $this->youtube = $youtube;
    }

    /**
     * @return  string $instagram
     */
    public function getInstagram()
    {
        return $this->instagram;
    }

    /**
     * @param  string  $instagram
     */
    public function setInstagram(string $instagram)
    {
        $this->instagram = $instagram;
    }

    /**
     * @return string $latitude
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param string $latitude
     * @return void
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return string $longitude
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param string $longitude
     * @return void
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return ObjectStorage<FileReference> $images
     */
    public function getImages(): ObjectStorage
    {
        return $this->images;
    }

    /**
     * @param ObjectStorage<FileReference> $images
     * @return void
     */
    public function setImages(FileReference $images)
    {
        $this->images = $images;
    }

    /**
     * @return string $slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return void
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @param Date $date
     * @return Event
     */
    public function addDate(Date $date): self
    {
        $this->dates->attach($date);
        return $this;
    }

    /**
     * @param Date $date
     * @return Event
     */
    public function removeDate(Date $date): self
    {
        $this->dates->detach($date);
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getDates(): ObjectStorage
    {
        return $this->dates;
    }

    /**
     * @param ObjectStorage $dates
     *
     * @return Event
     */
    public function setDates($dates): self
    {
        $this->dates = $dates;
        return $this;
    }

    /**
     * @param ObjectStorage $dates
     * @return void
     */
    public function removeAllDates(ObjectStorage $dates)
    {
        $this->dates->removeAll($dates);
    }

    /**
     * @return \Wrm\Events\Domain\Model\Organizer $organizer
     */
    public function getOrganizer()
    {
        return $this->organizer;
    }

    /**
     * @return ObjectStorage<Partner>
     */
    public function getPartner(): ObjectStorage
    {
        return $this->partner;
    }

    /**
     * @return ObjectStorage<Event>
     */
    public function getReferencesEvents(): ObjectStorage
    {
        return $this->referencesEvents;
    }

    /**
     * @param \Wrm\Events\Domain\Model\Organizer $organizer
     * @return void
     */
    public function setOrganizer(Organizer $organizer)
    {
        $this->organizer = $organizer;
    }

    /**
     * @return \Wrm\Events\Domain\Model\Region $region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param \Wrm\Events\Domain\Model\Region $region
     * @return void
     */
    public function setRegion(Region $region)
    {
        $this->region = $region;
    }

    /**
     * @return bool $highlight
     */
    public function getHighlight()
    {
        return $this->highlight;
    }

    /**
     * @param bool $highlight
     * @return void
     */
    public function setHighlight($highlight)
    {
        $this->highlight = $highlight;
    }

    /**
     * @return bool
     */
    public function isHighlight()
    {
        return $this->highlight;
    }

    /**
     * @return string $country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return void
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function getPages(): array
    {
        static $pages = null;
        if (is_array($pages)) {
            return $pages;
        }

        $pages = $this->dataProcessing->process($this);

        return $pages;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\Category<\TYPO3\CMS\Extbase\Domain\Model\Category> $category
     */
    public function addCategory(Category $category)
    {
        $this->categories->attach($category);
    }

    public function getCategories(): array
    {
        $categories = $this->categories->toArray();

        usort($categories, function (Category $catA, Category $catB) {
            return $catA->getSorting() <=> $catB->getSorting();
        });

        return $categories;
    }

    /**
     * @param TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category> $categories
     */
    public function setCategories(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $categories)
    {
        $this->categories = $categories;
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

    /**
     * @return int
     */
    public function getLocalizedUid()
    {
        return $this->_localizedUid;
    }
}
