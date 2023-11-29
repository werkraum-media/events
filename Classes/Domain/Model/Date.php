<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Domain\Model;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Date
 */
class Date extends AbstractEntity
{
    protected DateTime $start;

    protected ?DateTime $end = null;

    protected string $canceled = 'no';

    protected ?Date $postponedDate = null;

    protected ?Date $originalDate = null;

    /**
     * Can not be null in theory.
     * But editors might disable an event.
     * The date might still be available by Extbase, but without event.
     * This needs to be handled properly by consuming code for now.
     */
    protected ?Event $event;

    protected string $canceledLink = '';

    public function getStart(): DateTime
    {
        return $this->start;
    }

    public function setStart(DateTime $start): void
    {
        $this->start = $start;
    }

    public function getHasUsefulStartTime(): bool
    {
        return $this->getStart()->format('H:i') !== '00:00';
    }

    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    public function setEnd(?DateTime $end): void
    {
        $this->end = $end;
    }

    public function getHasUsefulEndTime(): bool
    {
        $end = $this->getEnd();
        return $end && $end->format('H:i') !== '23:59';
    }

    public function getEndsOnSameDay(): bool
    {
        $end = $this->getEnd();
        return $end && $this->getStart()->format('Y-m-d') === $end->format('Y-m-d');
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): self
    {
        $this->event = $event;
        return $this;
    }

    public function setLanguageUid(int $languageUid): void
    {
        $this->_languageUid = $languageUid;
    }

    public function getLanguageUid(): int
    {
        return $this->_languageUid;
    }

    public function getCanceled(): string
    {
        return $this->canceled;
    }

    public function setCanceled(string $canceled): void
    {
        $this->canceled = $canceled;
    }

    public function getPostponedDate(): ?Date
    {
        if ($this->getCanceled() === 'postponed') {
            return $this->postponedDate;
        }

        return null;
    }

    public function getOriginalDate(): ?Date
    {
        return $this->originalDate;
    }

    public function getCanceledLink(): string
    {
        if ($this->getCanceled() === 'canceled') {
            return $this->canceledLink;
        }

        return '';
    }

    public static function createFromDestinationDataDate(
        array $date,
        bool $canceled
    ): self {
        return self::createFromDestinationData(
            new DateTimeImmutable($date['start'], new DateTimeZone($date['tz'])),
            new DateTimeImmutable($date['end'], new DateTimeZone($date['tz'])),
            $canceled
        );
    }

    public static function createFromDestinationData(
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        bool $canceled
    ): self {
        $date = new Date();
        $date->setLanguageUid(-1);

        $date->setStart(new DateTime($start->format(DateTime::W3C), $start->getTimezone()));
        $date->setEnd(new DateTime($end->format(DateTime::W3C), $end->getTimezone()));

        if ($canceled) {
            $date->setCanceled('canceled');
        }

        return $date;
    }
}
