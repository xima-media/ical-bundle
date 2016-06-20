<?php

namespace Xima\ICalBundle\Entity\Property\Event;

use Xima\ICalBundle\Entity\Property\RecurrenceRule\NthOccurrence;

class RecurrenceRule extends \Eluceo\iCal\Property\Event\RecurrenceRule
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $byDays;

    public function __construct()
    {
        $this->byDays = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return array
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
     * @return array
     */
    public function getByWeekNo()
    {
        return $this->byWeekNo;
    }

    /**
     * @return array
     */
    public function getByYearDay()
    {
        return $this->byYearDay;
    }

    /**
     * @return array
     */
    public function getByMonthDay()
    {
        return $this->byMonthDay;
    }

    /**
     * @return array
     */
    public function getByHour()
    {
        return $this->byHour;
    }

    /**
     * @return array
     */
    public function getByMinute()
    {
        return $this->byMinute;
    }

    /**
     * @return array
     */
    public function getBySecond()
    {
        return $this->bySecond;
    }

    /**
     * Add byDay.
     *
     * @param NthOccurrence $byDay
     */
    public function addByDay(NthOccurrence $byDay)
    {
        $this->byDays[] = $byDay;
    }

    /**
     * Remove $byDay.
     *
     * @param NthOccurrence $byDay
     */
    public function removeByDay(NthOccurrence $byDay)
    {
        $this->byDays->removeElement($byDay);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getByDays()
    {
        return $this->byDays;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection
     */
    public function setByDays($byDays)
    {
        $this->byDays = $byDays;
    }

}
