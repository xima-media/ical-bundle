<?php

namespace Xima\ICalBundle\Entity\Component;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Request;

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
    
    public function __construct() {
        
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
    
    public static function generateUniqueId() {
        
        $request = Request::createFromGlobals();
        $uniqueId = time().'-'.get_current_user().'@'. $request->server->get('SERVER_NAME');
        
        return $uniqueId;
    }
}
