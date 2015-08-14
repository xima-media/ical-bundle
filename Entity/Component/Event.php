<?php

namespace Xima\ICalBundle\Entity\Component;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Request;
use Xima\ICalBundle\Entity\Property\Event\RecurrenceId;

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
    private $id;

    /**
     * @var \Event
     */
    private $parentEvent;

    /**
     * @var RecurrenceId
     */
    private $recurrenceId;

    public function __construct()
    {
        parent::__construct(Event::generateUniqueId());
    }
    
    public function __get($name)
    {
        return $this->$name;
    }
    
    public function __toString()
    {
        return get_class($this);
    }
    
    public static function generateUniqueId()
    {
        $request = Request::createFromGlobals();
        $uniqueId = time().'-'.get_current_user().'@'. $request->server->get('SERVER_NAME');
        
        return $uniqueId;
    }
    
    public function removeRecurrenceRule()
    {
        $this->recurrenceRule = null;
    
        return $this;
    }

    /**
     * @return \Event
     */
    public function getParentEvent()
    {
        return $this->parentEvent;
    }

    /**
     * @param \Event $parentEvent
     * @return Event
     */
    public function setParentEvent($parentEvent)
    {
        $this->parentEvent = $parentEvent;
        return $this;
    }

    /**
     * @return RecurrenceId
     */
    public function getRecurrenceId()
    {
        return $this->recurrenceId;
    }

    /**
     * @param RecurrenceId $recurrenceId
     * @return Event
     */
    public function setRecurrenceId($recurrenceId)
    {
        $this->recurrenceId = $recurrenceId;
        return $this;
    }
}
