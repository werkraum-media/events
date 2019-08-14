<?php
namespace Wrm\Events\Domain\Model;


/**
 * Date
 */
class Date extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
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
     * @var \Wrm\Events\Domain\Model\Event
     */
    protected $event = null;

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
     * @param string $end
     * @return void
     */
    public function setEnd($end)
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

}
