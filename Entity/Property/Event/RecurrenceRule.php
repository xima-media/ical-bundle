<?php

namespace Xima\ICalBundle\Entity\Property\Event;

class RecurrenceRule extends \Eluceo\iCal\Property\Event\RecurrenceRule
{
    /**
     * @var int
     */
    private $id;

    /**
     * An empty freq value is only allowed for this bundle.
     *
     * @see \Eluceo\iCal\Property\Event\RecurrenceRule::setFreq()
     */
    public function setFreq($freq)
    {
        $this->freq = $freq;

        return $this;
    }

    public function __toString()
    {
        return get_class($this);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return NULL|string
     */
    public function getByDay()
    {
        return $this->byDay;
    }

    /**
     * @return NULL|string
     */
    public function getByMonth()
    {
        return $this->byMonth;
    }

    /**
     * @return NULL|string
     */
    public function getByWeekNo()
    {
        return $this->byWeekNo;
    }

    /**
     * @return NULL|string
     */
    public function getByYearDay()
    {
        return $this->byYearDay;
    }

    /**
     * @return NULL|string
     */
    public function getByMonthDay()
    {
        return $this->byMonthDay;
    }
}
