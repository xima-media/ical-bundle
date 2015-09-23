<?php

namespace Xima\ICalBundle\Util;

use Doctrine\ORM\EntityManagerInterface;
use Xima\ICalBundle\Entity\Component\Event;
use Eluceo\iCal\Component\Calendar;
use Sabre\VObject;

class EventUtil
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns an array with all instances of a recurring event.
     *
     * todo: Refactor - combining eluceo/ical and sabre/vobject this way seems very expensive.
     * @param Event $event
     * @param bool|true $includeEditedEvents
     * @param \DateTime|null $dateFrom
     * @param \DateTime|null $dateTo
     *
     * @return Event[]
     *
     * @throws \Exception
     */
    public function getInstances(Event $event, $includeEditedEvents, \DateTime $dateFrom = null, \DateTime $dateTo = null)
    {
        $dateFrom = ($dateFrom)? $dateFrom : $event->getDtStart();
        if (!$dateTo) {
            $dateTo = clone ($event->getDtStart());
            $dateTo->add(new \DateInterval('P1Y'));
        }

        if (!$dateFrom || !$dateTo) {
            throw new \Exception('Trying to get instances of a recurring event without dateFrom and/or dateTo being set.');
        }

        $instances = array();

        //create the calendar
        $vCalendar = new Calendar($event->getUniqueId());
        $vCalendar->addComponent($event);

        //get edited events: depending on $includeEditedEvents to mark these events as deleted or to replace them by their edited event
        $editedEventsByTimestamp = array();
        $qb = $this->entityManager->createQueryBuilder();
        $qb ->select('e')
            ->from('Xima\ICalBundle\Entity\Component\Event', 'e')
            ->where('e.uniqueId = :uniqueId')
            ->andWhere($qb->expr()->isNotNull('e.recurrenceId'))
            ->setParameter('uniqueId', $event->getUniqueId());
        $editedEvents = $qb->getQuery()->getResult();

        foreach ($editedEvents as $editedEvent) {
            /* @var $editedEvent \Xima\XRBSBundle\Entity\Event */
            $vCalendar->addComponent($editedEvent);
            $editedEventsByTimestamp[$editedEvent->getDtStart()->getTimestamp()] = $editedEvent;
        }

        //render the calendar and parse it to get all recurrences of the event
        $vCalendarExpandedData = $vCalendar->render();
        $vCalendarExpanded = VObject\Reader::read($vCalendarExpandedData);

        /* @var $vCalendarExpanded \Sabre\VObject\Component\VCalendar */
        $vCalendarExpanded->expand($dateFrom, $dateTo);
        foreach ($vCalendarExpanded->getComponents() as $instanceComp) {
            /* @var $instanceComp \Sabre\VObject\Component\VEvent */
            $instance = null;
            if (isset($editedEventsByTimestamp[$instanceComp->DTSTART->getDateTime()->getTimestamp()])) {
                if ($includeEditedEvents) {
                    $instance = clone $editedEventsByTimestamp[$instanceComp->DTSTART->getDateTime()->getTimestamp()];
                } else {
                    continue;
                }
            } else {
                $instance = clone $event;
            }

            if ($instance) {
                $instance->setDtStart($instanceComp->DTSTART->getDateTime());
                $instance->setDtEnd($instanceComp->DTEND->getDateTime());

                $instances[] = $instance;
            }
        }

        return $instances;
    }

    /**
     * Removes recurring rule if empty and nullifies "byDay" property if empty.
     *
     * @param Event $event
     */
    public function cleanUpEvent(Event $event)
    {
        if ($event->getRecurrenceRule()) {
            if ('' == $event->getRecurrenceRule()->getFreq()) {
                $this->entityManager->remove($event->getRecurrenceRule());
                $event->removeRecurrenceRule();
            } elseif ('' == $event->getRecurrenceRule()->getByDay()) {
                $event->getRecurrenceRule()->setByDay(null);
            }
        }
    }
}
