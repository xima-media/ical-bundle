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
     * @var bool
     */
    private $isAllDayEvent;

    /**
     * @var \DateTime
     */
    private $allDayStart;

    /**
     * @var \DateTime
     */
    private $allDayEnd;

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

    public function postLoad()
    {
        //this->exDates: convert json to DateTimes
        $exDatesJson = $this->exDates;
        $this->exDates = array();

        foreach ($exDatesJson as $exDateJson) {
            $dateTime = new \DateTime($exDateJson['date'], new \DateTimeZone($exDateJson['timezone']));
            $this->exDates[] = $dateTime;
        }
    }

    public function removeRecurrenceRule()
    {
        $this->recurrenceRule = null;

        return $this;
    }

    public function getIsAllDayEvent()
    {
        if ($this->getDtStart() && $this->getDtStart()->format("H:i") == "00:00" &&  $this->getDtEnd() && $this->getDtEnd()->format("H:i") == "00:00") {
            return true;
        }
        return false;
    }
    /**
     * Set all day.
     *
     * @param bool $isAllDayEvent
     */
    public function setIsAllDayEvent($isAllDayEvent)
    {
        $this->isAllDayEvent = $isAllDayEvent;
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

    /**
     * @return \DateTime
     */
    public function getAllDayStart()
    {
        return $this->dtStart;
    }

    /**
     * @param \DateTime $allDayStart
     */
    public function setAllDayStart($allDayStart)
    {
        $this->allDayStart = $allDayStart;
    }

    /**
     * @return \DateTime
     */
    public function getAllDayEnd()
    {
        return $this->dtEnd;
    }

    /**
     * @param \DateTime $allDayEnd
     */
    public function setAllDayEnd($allDayEnd)
    {
        $this->allDayEnd = $allDayEnd;
    }

}
