<?php

namespace Xima\ICalBundle\Entity\Component;

use Symfony\Component\HttpFoundation\Request;
use Xima\ICalBundle\Entity\Property\Event\RecurrenceRule;

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
    protected $id;

    /**
     * @var \DateTime
     */
    protected $dateFrom;

    /**
     * @var \DateTime
     */
    protected $timeFrom;

    /**
     * @var \DateTime
     */
    protected $dateTo;

    /**
     * @var \DateTime
     */
    protected $timeTo;

    /**
     * @var RecurrenceRule
     */
    protected $recurrenceRule;


    public function __construct()
    {
        $this->created = new \DateTime();
        parent::__construct(self::generateUniqueId());
    }

    public function __toString()
    {
        return get_class($this);
    }

    public static function generateUniqueId()
    {
        $request = Request::createFromGlobals();
        $uniqueId = time() . '-' . bin2hex(get_current_user()) . '-' .  mt_rand() . '@' . $request->server->get('SERVER_NAME');

        return $uniqueId;
    }

    public function postLoad()
    {
        $exDatesJson = $this->exDates;
        $this->exDates = array();

        //this->exDates: convert json to DateTimes, can be null or empty array
        if (!empty($exDatesJson) && !is_null($exDatesJson)) {
            foreach ($exDatesJson as $exDateJson) {
                $dateTime = new \DateTime($exDateJson['date'], new \DateTimeZone($exDateJson['timezone']));
                $this->exDates[] = $dateTime;
            }
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

    /**
     * @return RecurrenceRule
     */
    public function getRecurrenceRule()
    {
        return $this->recurrenceRule;
    }
}
