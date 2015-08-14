<?php

namespace Xima\ICalBundle\Entity\Property\Event;

class RecurrenceRule extends \Eluceo\iCal\Property\Event\RecurrenceRule
{
    /**
     * @var int
     */
    private $id;

    public function __get($name)
    {
        return $this->$name;
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

    /**
     * Make the $month value an integer to make Sonata compatible to iCal package.
     *
     * @see \Eluceo\iCal\Property\Event\RecurrenceRule::setByMonth()
     */
    public function setByMonth($month)
    {
        parent::setByMonth(intval($month));
    }

    public function __toString()
    {
        return get_class($this);
    }
    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
