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
    protected $start = null;

    /**
     * @var \DateTime
     */
    protected $end = null;

    /**
     * @var string
     */
    protected $canceled = "no";

    /**
     * @var null|Date
     */
    protected $postponedDate;

    /**
     * @var null|Date
     */
    protected $originalDate;

    /**
     * @var \Wrm\Events\Domain\Model\Event
     */
    protected $event = null;

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
     * @return void
     */
    public function setStart(\DateTime $start)
    {
        $this->start = $start;
    }

    /**
     * @return \DateTime end
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     * @return void
     */
    public function setEnd(\DateTime $end)
    {
        $this->end = $end;
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
}
