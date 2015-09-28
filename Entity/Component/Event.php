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
     * @var \DateTime
     */
    private $dateFrom;

    /**
     * @var \DateTime
     */
    private $timeFrom;

    /**
     * @var \DateTime
     */
    private $dateTo;

    /**
     * @var \DateTime
     */
    private $timeTo;

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
     * @return boolean
     */
    public function isNoTime()
    {
        return $this->noTime;
    }

    /**
     * @return mixed
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * @param mixed $dateFrom
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * @return mixed
     */
    public function getTimeFrom()
    {
        return $this->timeFrom;
    }

    /**
     * @param mixed $timeFrom
     */
    public function setTimeFrom($timeFrom)
    {
        $this->timeFrom = $timeFrom;
    }

    /**
     * @return mixed
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * @param mixed $dateTo
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;
    }

    /**
     * @return mixed
     */
    public function getTimeTo()
    {
        return $this->timeTo;
    }

    /**
     * @param mixed $timeTo
     */
    public function setTimeTo($timeTo)
    {
        $this->timeTo = $timeTo;
    }
}
