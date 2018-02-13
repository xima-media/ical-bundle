<?php

namespace Xima\ICalBundle\Entity\Property\Event;

class RecurrenceId extends \Eluceo\iCal\Property\Event\RecurrenceId
{
    /**
     * @var int
     */
    protected $id;

    public function postLoad()
    {
        parent::__construct();
    }
}
