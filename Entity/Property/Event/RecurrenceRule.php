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

    /**
     * Add byDay.
     *
     * @param NthOccurrence $byDay
     *
     * @return RecurrenceRule
     */
    public function addByDay(NthOccurrence $byDay)
    {
        $this->byDays[] = $byDay;

        return $this;
    }

    /**
     * Remove $byDay.
     *
     * @param NthOccurrence $byDay
     */
    public function removeSpeaker(NthOccurrence $byDay)
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
     * @param \Doctrine\Common\Collections\Collection $byDays
     */
    public function setByDays($byDays)
    {
        $this->byDays = $byDays;
    }
}
