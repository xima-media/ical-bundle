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
     * Make the $month value an integer to make Sonata compatible to iCal package.
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
