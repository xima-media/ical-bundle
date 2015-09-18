<?php

namespace Xima\ICalBundle\Util;

use Doctrine\ORM\EntityManagerInterface;
use Xima\ICalBundle\Entity\Component\Event;

class EventUtil
{
    public static function cleanUpEvent(Event $event, EntityManagerInterface $em)
    {
        if ($event->getRecurrenceRule()) {
            if ('' == $event->getRecurrenceRule()->getFreq()) {
                $event->removeRecurrenceRule();
                $event->removeRecurrenceRule();
            } elseif ('' == $event->getRecurrenceRule()->getByDay()) {
                $event->getRecurrenceRule()->setByDay(null);
            }
        }
    }
}
