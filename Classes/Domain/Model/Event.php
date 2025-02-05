<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation\ORM\Cascade;
use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use WerkraumMedia\Events\Service\DataProcessingForModels;

class Event extends AbstractEntity
{
    protected string $title = '';

    protected string $subtitle = '';

    protected string $globalId = '';

    protected string $slug = '';

    protected bool $highlight = false;

    protected string $teaser = '';

    protected string $details = '';

    protected string $priceInfo = '';

    protected string $web = '';

    protected string $ticket = '';

    protected string $facebook = '';

    protected string $youtube = '';

    protected string $instagram = '';

    /**
     * @var ObjectStorage<FileReference>
     */
    #[Cascade(['value' => 'remove'])]
    protected ObjectStorage $images;

    /**
     * @var ObjectStorage<Date>
     */
    #[Cascade(['value' => 'remove'])]
    #[Lazy]
    protected ObjectStorage $dates;

    protected ?Location $location = null;

    protected ?Organizer $organizer = null;

    protected ?Region $region = null;

    protected string $pages = '';

    protected array $resolvedPages = [];

    /**
     * @var ObjectStorage<Category>
     */
    protected ObjectStorage $categories;

    /**
     * @var ObjectStorage<Category>
     */
    protected ObjectStorage $features;

    protected string $keywords = '';

    /**
     * @var ObjectStorage<Partner>
     */
    protected ObjectStorage $partner;

    /**
     * @var ObjectStorage<Event>
     */
    protected ObjectStorage $referencesEvents;

    protected DataProcessingForModels $dataProcessing;

    protected string $sourceName = '';

    protected string $sourceUrl = '';

    public function __construct()
    {
        $this->initStorageObjects();
        $this->initializeDataProcessing();
    }

    public function initializeObject(): void
    {
        $this->initStorageObjects();
        $this->initializeDataProcessing();
    }

    private function initializeDataProcessing(): void
    {
        $this->dataProcessing = GeneralUtility::makeInstance(DataProcessingForModels::class);
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
     * @return ObjectStorage<FileReference>
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
        if ($this->resolvedPages !== []) {
            return $this->resolvedPages;
        }

        $this->resolvedPages = $this->dataProcessing->process($this);

        return $this->resolvedPages;
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
        return $this->getSortedCategory($this->categories);
    }

    /**
     * @return array<Category>
     */
    public function getFeatures(): array
    {
        return $this->getSortedCategory($this->features);
    }

    public function getKeywords(): string
    {
        return $this->keywords;
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

    public function setSourceName(string $name): void
    {
        $this->sourceName = $name;
    }

    public function setSourceUrl(string $url): void
    {
        $this->sourceUrl = $url;
    }

    private function getSortedCategory(ObjectStorage $categories): array
    {
        $categories = $categories->toArray();

        usort($categories, fn (Category $catA, Category $catB) => $catA->getSorting() <=> $catB->getSorting());

        return $categories;
    }
}
