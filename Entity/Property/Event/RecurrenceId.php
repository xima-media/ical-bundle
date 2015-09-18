<?php

namespace Xima\ICalBundle\Entity\Property\Event;

class RecurrenceId extends \Eluceo\iCal\Property\Event\RecurrenceId
{
    /**
     * @var int
     */
    private $id;

    public function postLoad()
    {
        parent::__construct();
    }
}
