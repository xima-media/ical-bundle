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
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Returns an array with all instances of a recurring event. If an event was detached it's not part of the series' instances.
     *
     * todo: Refactor - combining eluceo/ical and sabre/vobject this way seems very expensive.
     * @param Event $event
     * @param \DateTime|null $dateFrom
     * @param \DateTime|null $dateTo
     *
     * @return Event[]
     *
     * @throws \Exception
     */
    public function getInstances(Event $event, \DateTime $dateFrom = null, \DateTime $dateTo = null)
    {
        $instances = array();

        $dateFrom = ($dateFrom)? $dateFrom : $event->getDtStart();
        if (!$dateFrom) {
            return $instances;
        }
        if (!$dateTo) {
            //default to one year if no end is set
            $dateTo = clone ($event->getDtStart());
            $dateTo->add(new \DateInterval('P1Y'));
        }

        if (!$dateFrom || !$dateTo) {
            throw new \Exception('Trying to get instances of a recurring event without dateFrom and/or dateTo being set.');
        }

        //create the calendar
        $vCalendar = new Calendar($event->getUniqueId());
        $vCalendar->addComponent($event);

        //get edited events: depending on $includeEditedEvents to mark these events as deleted or to replace them by their edited event
        $editedEventsByTimestamp = array();
        $qb = $this->em->createQueryBuilder();
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
                    continue;
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
     * Update all properties to ical requirements and apply changes to dependent attributes:
     * - set $dtStart and $dtEnd according to the selected values from $dateFrom, $timeFrom, $dateTo and $timeTo
     * - remove $recurrenceRule if it's $frequency is empty
     * - set $recurrenceRule's $byDay value to null if empty
     * - apply changed $dtStart values to $excludedDates
     * Must be executed on persisting the event. Should be called prior to any validation.
     *
     * @param Event $event
     */
    public function cleanUpEvent(Event $event)
    {
        //merge dates and times, apply noTime setting
        $event->setDtStart($event->getDateFrom());
        if (is_null($event->getTimeFrom())) {
            $event->getDtStart()->setTime(0, 0);
            $event->setTimeFrom(new \DateTime('1970-01-01'));
        } else {
            $event->getDtStart()->setTime($event->getTimeFrom()->format('H'), $event->getTimeFrom()->format('i'));
        }
        $event->setDtEnd(clone($event->getDateTo()));
        if (is_null($event->getTimeTo())) {
            $event->getDtEnd()->setTime(0, 0);
            $event->setTimeTo(new \DateTime('1970-01-01'));
        } else {
            $event->getDtEnd()->setTime($event->getTimeTo()->format('H'), $event->getTimeTo()->format('i'));
        }

        if ($event->isNoTime()) {
            $event->getDtEnd()->add(new \DateInterval('P1D'));
        }

        //do the following only if event is recurring
        if ($event->getRecurrenceRule()) {
            //remove recurring rule if empty and nullify "byDay" property if empty
            if ('' == $event->getRecurrenceRule()->getFreq()) {
                $this->em->remove($event->getRecurrenceRule());
                $event->removeRecurrenceRule();
            } elseif ('' == $event->getRecurrenceRule()->getByDay()) {
                $event->getRecurrenceRule()->setByDay(null);
            }

            //update excludeDates
            /** @var $entity \Xima\ICalBundle\Entity\Component\Event */
            $uow = $this->em->getUnitOfWork();

            $changeSet = $uow->getEntityChangeSet($event);
            if (isset($changeSet['dtStart']) && isset($changeSet['dtStart'][0])) {
                $interval = $changeSet['dtStart'][0]->diff($event->getDtStart());
                foreach ($event->getExDates() as $dateTime) {
                    $dateTime->add($interval);
                }
            }
        }
    }
}
