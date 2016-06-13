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
    protected $em;

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
    public function getInstances(Event $event, \DateTime $dateFrom = null, \DateTime $dateTo = null, $findOnlyOne = false)
    {
        $eventInstances = array();

        if (!$event->getRecurrenceRule()) {
            return array($event);
        }

        $dateFrom = ($dateFrom) ? $dateFrom : $event->getDtStart();
        if (!$dateFrom) {
            return $eventInstances;
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
        if (self::isValidDateTime($event->getDtStart()) && self::isValidDateTime($event->getDtEnd())) {
            $vCalendar->addComponent($event);
        }

        //get edited events: depending on $includeEditedEvents to mark these events as deleted or to replace them by their edited event
        $editedEventsByTimestamp = array();
        $qb = $this->em->createQueryBuilder();
        $qb->select('e')
            ->from('Xima\ICalBundle\Entity\Component\Event', 'e')
            ->where('e.uniqueId = :uniqueId')
            ->andWhere($qb->expr()->isNotNull('e.recurrenceId'))
            ->setParameter('uniqueId', $event->getUniqueId());
        $editedEvents = $qb->getQuery()->getResult();

        foreach ($editedEvents as $editedEvent) {
            /* @var $editedEvent \Xima\ICalBundle\Entity\Component\Event */
            if (self::isValidDateTime($editedEvent->getDtStart()) && self::isValidDateTime($editedEvent->getDtEnd()) && self::isValidDateTime($editedEvent->getRecurrenceId()->getDatetime())) {
                $editedEventsByTimestamp[$editedEvent->getDtStart()->getTimestamp()] = $editedEvent;
                $vCalendar->addComponent($editedEvent);
            }
        }

        //render the calendar and parse it to get all recurrences of the event
        $vCalendarExpandedData = $vCalendar->render();
        $vCalendarExpanded = VObject\Reader::read($vCalendarExpandedData);
        /* @var $vCalendarExpanded \Sabre\VObject\Component\VCalendar */
        $vCalendarExpanded = $vCalendarExpanded->expand($dateFrom, $dateTo);
        foreach ($vCalendarExpanded->getComponents() as $instanceComp) {
            /* @var $instanceComp \Sabre\VObject\Component\VEvent */
            // It's basically the same event, but with the new calculated dates and times...
            // @todo: refactor so that dtStart is set when dateFrom gets set and so on...
            $dtStart = new \DateTime();
            $dtStart->setTimestamp($instanceComp->DTSTART->getDateTime()->getTimestamp());
            $dtEnd = new \DateTime();
            $dtEnd->setTimestamp($instanceComp->DTEND->getDateTime()->getTimestamp());

            $eventInstance = clone $event;
            /* @var $eventInstance Event */
            $eventInstance->setDtStart($dtStart);
            $eventInstance->setDateFrom($dtStart);
            $eventInstance->setTimeFrom($dtStart);
            $eventInstance->setDtEnd($dtEnd);
            $eventInstance->setDateTo($dtEnd);
            $eventInstance->setTimeTo($dtEnd);

            if ($findOnlyOne){
                return array($eventInstance);
            }

            if (isset($editedEventsByTimestamp[$instanceComp->DTSTART->getDateTime()->getTimestamp()])) {
                // if the instance was detached, it's not part of the series' instances
                continue;
            } else {
                $eventInstances[] = $eventInstance;
            }
        }

        return $eventInstances;
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
        if ($event->getDateFrom()) {
            //merge dates and times, apply noTime setting
            $event->setDtStart($event->getDateFrom());
            if (is_null($event->getTimeFrom())) {
                $event->getDtStart()->setTime(0, 0);
                $event->setTimeFrom(new \DateTime('1970-01-01'));
            } else {
                $event->getDtStart()->setTime($event->getTimeFrom()->format('H'), $event->getTimeFrom()->format('i'));
            }
        }
        if ($event->getDateTo()) {
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

    /**
     * Check if date value is '0000-00-00'
     * @see http://stackoverflow.com/questions/10450644/how-do-you-explain-the-result-for-a-new-datetime0000-00-00-000000
     * @todo: refactor?
     *
     * @param \DateTime $dateTime
     * @return bool
     */
    public static function isValidDateTime(\DateTime $dateTime)
    {
        if ($dateTime->format('U') == -62169984000) {
            return false;
        }

        return true;
    }
}
