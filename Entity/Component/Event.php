<?php

namespace Xima\ICalBundle\Entity\Component;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Wolfram Eberius <wolfram.eberius@xima.de>
 * @todo Map missing properties: attendees and categories
 *
 */
class Event extends \Eluceo\iCal\Component\Event
{
    /**
     * @var int
     */
    protected $id;
    
    public function __get($name)
    {
        return $this->$name;
    }
    
    public function __toString()
    {
        return get_class($this);
    }
}
