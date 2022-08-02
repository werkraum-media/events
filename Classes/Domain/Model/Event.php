<?php

namespace Wrm\Events\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use Wrm\Events\Domain\Repository\DateRepository;
use Wrm\Events\Service\DataProcessingForModels;

class Event extends AbstractEntity
{
    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $subtitle = '';

    /**
     * @var string
     */
    protected $globalId = '';

    /**
     * @var string
     */
    protected $slug = '';

    /**
     * @var bool
     */
    protected $highlight = false;

    /**
     * @var string
     */
    protected $teaser = '';

    /**
     * @var string
     */
    protected $details = '';

    /**
     * @var string
     */
    protected $priceInfo = '';

    /**
     * @var string
     */
    protected $web = '';

    /**
     * @var string
     */
    protected $ticket = '';

    /**
     * @var string
     */
    protected $facebook = '';

    /**
     * @var string
     */
    protected $youtube = '';

    /**
     * @var string
     */
    protected $instagram = '';

    /**
     * @var ObjectStorage<FileReference>
     * @Extbase\ORM\Cascade remove
     */
    protected $images;

    /**
     * @var ObjectStorage<Date>
     * @Extbase\ORM\Cascade remove
     * @Extbase\ORM\Lazy
     */
    protected $dates;

    /**
     * @var Location|null
     */
    protected $location = null;

    /**
     * @var Organizer|null
     */
    protected $organizer = null;

    /**
     * @var Region|null
     */
    protected $region = null;

    /**
     * @var string
     */
    protected $pages = '';

    /**
     * @var ObjectStorage<Category>
     */
    protected $categories;

    /**
     * @var ObjectStorage<Category>
     */
    protected $features;

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

    // Legacy location related info

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
    protected $district = '';

    /**
     * @var string
     */
    protected $city = '';

    /**
     * @var string
     */
    protected $zip = '';

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

    // End of legacy location info

    public function __construct()
    {
        $this->initStorageObjects();
    }

    /**
     * @param DataProcessingForModels $dataProcessing
     */
    public function injectDataProcessingForModels(DataProcessingForModels $dataProcessing): void
    {
        $this->dataProcessing = $dataProcessing;
    }

    public function initializeObject(): void
    {
        $this->initStorageObjects();
    }

    protected function initStorageObjects(): void
    {
        $this->images = new ObjectStorage();
        $this->dates = new ObjectStorage();
        $this->categories = new ObjectStorage();
        $this->features = new ObjectStorage();
        $this->partner = new ObjectStorage();
        $this->referencesEvents = new ObjectStorage();
    }

    public function getGlobalId(): string
    {
        return $this->globalId;
    }

    public function setGlobalId(string $globalId): void
    {
        $this->globalId = $globalId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    public function getTeaser(): string
    {
        return $this->teaser;
    }

    public function setTeaser(string $teaser): void
    {
        $this->teaser = $teaser;
    }

    public function getDetails(): string
    {
        return $this->details;
    }

    public function setDetails(string $details): void
    {
        $this->details = $details;
    }

    public function getPriceInfo(): string
    {
        return $this->priceInfo;
    }

    public function setPriceInfo(string $priceInfo): void
    {
        $this->priceInfo = $priceInfo;
    }

    public function getWeb(): string
    {
        return $this->web;
    }

    public function setWeb(string $web): void
    {
        $this->web = $web;
    }

    public function getTicket(): string
    {
        return $this->ticket;
    }

    public function setTicket(string $ticket): void
    {
        $this->ticket = $ticket;
    }

    public function getFacebook(): string
    {
        return $this->facebook;
    }

    public function setFacebook(string $facebook): void
    {
        $this->facebook = $facebook;
    }

    public function getYoutube(): string
    {
        return $this->youtube;
    }

    public function setYoutube(string $youtube): void
    {
        $this->youtube = $youtube;
    }

    public function getInstagram(): string
    {
        return $this->instagram;
    }

    public function setInstagram(string $instagram): void
    {
        $this->instagram = $instagram;
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
     */
    public function setImages(ObjectStorage $images): void
    {
        $this->images = $images;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function addDate(Date $date): void
    {
        $this->dates->attach($date);
    }

    public function removeDate(Date $date): void
    {
        $this->dates->detach($date);
    }

    /**
     * @return ObjectStorage<Date>
     */
    public function getDates(): ObjectStorage
    {
        return $this->dates;
    }

    /**
     * @param ObjectStorage<Date> $dates
     */
    public function setDates(ObjectStorage $dates): void
    {
        $this->dates = $dates;
    }

    /**
     * @param ObjectStorage<Date> $dates
     */
    public function removeAllDates(ObjectStorage $dates): void
    {
        $this->dates->removeAll($dates);
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

    public function setLocation(?Location $location): void
    {
        $this->location = $location;

        // Keep this block as long as event still has the properties for legacy reasons.
        // This ensures there is only a single point of truth, the new location object.
        // That way we detect issues earlier and can migrate them until we get rid of the legacy code base.
        if ($location instanceof Location) {
            $this->name = '';
            $this->street = '';
            $this->district = '';
            $this->city = '';
            $this->zip = '';
            $this->country = '';
            $this->phone = '';
            $this->latitude = '';
            $this->longitude = '';
        }
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setOrganizer(Organizer $organizer): void
    {
        $this->organizer = $organizer;
    }

    public function getOrganizer(): ?Organizer
    {
        return $this->organizer;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(Region $region): void
    {
        $this->region = $region;
    }

    public function setHighlight(bool $highlight): void
    {
        $this->highlight = $highlight;
    }

    public function isHighlight(): bool
    {
        return $this->highlight;
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

    public function addCategory(Category $category): void
    {
        $this->categories->attach($category);
    }

    /**
     * @return array<Category>
     */
    public function getCategories(): array
    {
        $categories = $this->categories->toArray();

        usort($categories, function (Category $catA, Category $catB) {
            return $catA->getSorting() <=> $catB->getSorting();
        });

        return $categories;
    }

    /**
     * @param ObjectStorage<Category> $categories
     */
    public function setCategories(ObjectStorage $categories): void
    {
        $this->categories = $categories;
    }

    /**
     * @return array<Category>
     */
    public function getFeatures(): array
    {
        $features = $this->features->toArray();

        usort($features, function (Category $catA, Category $catB) {
            return $catA->getSorting() <=> $catB->getSorting();
        });

        return $features;
    }

    /**
     * @param ObjectStorage<Category> $features
     */
    public function setFeatures(ObjectStorage $features): void
    {
        $this->features = $features;
    }

    public function setLanguageUid(int $languageUid): void
    {
        $this->_languageUid = $languageUid;
    }

    public function getLanguageUid(): int
    {
        return $this->_languageUid;
    }

    public function getLocalizedUid(): int
    {
        return $this->_localizedUid;
    }

    // Legacy location related info

    public function getName(): string
    {
        if ($this->location instanceof Location) {
            return $this->location->getName();
        }

        return $this->name;
    }

    public function getStreet(): string
    {
        if ($this->location instanceof Location) {
            return $this->location->getStreet();
        }

        return $this->street;
    }

    public function getDistrict(): string
    {
        if ($this->location instanceof Location) {
            return $this->location->getDistrict();
        }

        return $this->district;
    }

    public function getCountry(): string
    {
        if ($this->location instanceof Location) {
            return $this->location->getCountry();
        }

        return $this->country;
    }

    public function getCity(): string
    {
        if ($this->location instanceof Location) {
            return $this->location->getCity();
        }

        return $this->city;
    }

    public function getZip(): string
    {
        if ($this->location instanceof Location) {
            return $this->location->getZip();
        }

        return $this->zip;
    }

    public function getPhone(): string
    {
        if ($this->location instanceof Location) {
            return $this->location->getPhone();
        }

        return $this->phone;
    }

    public function getLatitude(): string
    {
        if ($this->location instanceof Location) {
            return $this->location->getLatitude();
        }

        return $this->latitude;
    }

    public function getLongitude(): string
    {
        if ($this->location instanceof Location) {
            return $this->location->getLongitude();
        }

        return $this->longitude;
    }
}
