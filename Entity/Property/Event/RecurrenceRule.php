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
     * {@inheritdoc }
     */
    protected $freq = null;

    /**
     * {@inheritdoc }
     */
    protected $interval = null;

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

    public function postLoad()
    {
        $this->convertByDaysToByDay();
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
    public function addByDays(NthOccurrence $byDay)
    {
        $this->byDays[] = $byDay;

        $this->convertByDaysToByDay();
    }

    /**
     * Remove $byDay.
     *
     * @param NthOccurrence $byDay
     */
    public function removeByDays(NthOccurrence $byDay)
    {
        $this->byDays->removeElement($byDay);

        $this->convertByDaysToByDay();
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

        $this->convertByDaysToByDay();
    }

    /**
     * Turns the collections to arrays.
     */
    protected function convertByDaysToByDay()
    {
        $byDays = array();
        foreach ($this->byDays as $bD)
        {
            /* @var $bD NthOccurrence */
            $byDays[] = $bD->getNth() . $bD->getOccurrence();
        }

        if (!empty($byDays)){
            parent::setByDay(implode(', ', $byDays));
        }
    }

}
