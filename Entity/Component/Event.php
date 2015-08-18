<?php

namespace Xima\ICalBundle\Entity\Component;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Wolfram Eberius <wolfram.eberius@xima.de>
 *
 * @todo Map missing properties: attendees and categories
 */
class Event extends \Eluceo\iCal\Component\Event
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \Event
     */
    private $parentEvent;

    public function __construct()
    {
        parent::__construct(self::generateUniqueId());
    }

    public function __toString()
    {
        return get_class($this);
    }

    public static function generateUniqueId()
    {
        $request = Request::createFromGlobals();
        $uniqueId = time().'-'.get_current_user().'@'.$request->server->get('SERVER_NAME');

        return $uniqueId;
    }

    public function removeRecurrenceRule()
    {
        $this->recurrenceRule = null;

        return $this;
    }

    public function prePersist()
    {
        $this->preSave();
    }

    public function preUpdate()
    {
        $this->preSave();
    }

    private function preSave()
    {
        //this->exDates: convert DateTimes to Timestamps
        $exDates = $this->exDates;
        $this->exDates = array();

        foreach ($exDates as $exDate) {
            $this->exDates[] = $exDate->getTimestamp();
        }
    }

    public function postLoad()
    {
        //this->exDates: convert Timestamps to DateTimes
        $exDates = $this->exDates;
        $this->exDates = array();

        foreach ($exDates as $exDate) {
            $dateTime = new \DateTime();
            $dateTime->setTimestamp($exDate);
            $this->exDates[] = $dateTime;
        }
    }

    /**
     * @return \Event
     */
    public function getParentEvent()
    {
        return $this->parentEvent;
    }

    /**
     * @param \Event $parentEvent
     *
     * @return Event
     */
    public function setParentEvent($parentEvent)
    {
        $this->parentEvent = $parentEvent;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDtStart()
    {
        return $this->dtStart;
    }
}
