<?php

namespace Wrm\Events\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Date
 */
class Date extends AbstractEntity
{
    /**
     * @var \DateTime
     */
    protected $start;

    /**
     * @var \DateTime|null
     */
    protected $end;

    /**
     * @var string
     */
    protected $canceled = 'no';

    /**
     * @var Date|null
     */
    protected $postponedDate;

    /**
     * @var Date|null
     */
    protected $originalDate;

    /**
     * @var \Wrm\Events\Domain\Model\Event
     */
    protected $event;

    /**
     * @var string
     */
    protected $canceledLink = '';

    /**
     * @var int
     */
    protected $_languageUid;

    /**
     * @return \DateTime $start
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     *
     * @return void
     */
    public function setStart(\DateTime $start)
    {
        $this->start = $start;
    }

    public function getHasUsefulStartTime(): bool
    {
        return $this->getStart()->format('H:i') !== '00:00';
    }

    /**
     * @return \DateTime|null end
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param \DateTime|null $end
     *
     * @return void
     */
    public function setEnd($end)
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

    /**
     * @return Event
     */
    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * @param Event $event
     */
    public function setEvent(Event $event): self
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @param int $languageUid
     *
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
     * @return  string
     */
    public function getCanceled(): string
    {
        return $this->canceled;
    }

    /**
     * @param  string  $canceled
     *
     * @return void
     */
    public function setCanceled(string $canceled)
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
            new \DateTimeImmutable($date['start'], new \DateTimeZone($date['tz'])),
            new \DateTimeImmutable($date['end'], new \DateTimeZone($date['tz'])),
            $canceled
        );
    }

    public static function createFromDestinationData(
        \DateTimeImmutable $start,
        \DateTimeImmutable $end,
        bool $canceled
    ): self {
        $date = new Date();
        $date->setLanguageUid(-1);

        $date->setStart(new \DateTime($start->format(\DateTime::W3C), $start->getTimezone()));
        $date->setEnd(new \DateTime($end->format(\DateTime::W3C), $end->getTimezone()));

        if ($canceled) {
            $date->setCanceled('canceled');
        }

        return $date;
    }
}
