<?php

namespace Xima\ICalBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Xima\ICalBundle\Util\EventUtil;
use Xima\XRBSBundle\Entity\Room;

class EventRepository extends EntityRepository
{
    /**
     * @var QueryBuilder
     */
    private $qb;

    /**
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     * @param Room      $room
     *
     * @return Array
     */
    public function getEventsByDate(\DateTime $dateFrom, \DateTime $dateTo, Room $room = null)
    {
        // provide old getEventsByDate function

        // prepare $qb with usual parameters and querries
        $this->qb = $this->getEntityManager()->createQueryBuilder()->select('event')->from($this->getEntityName(), 'event');

        $this->qb->innerJoin('event.booking', 'booking');
        $this->qb->setParameter('dateFrom', $dateFrom);
        $this->qb->setParameter('dateTo', $dateTo);

        if (isset($room)) {
            $this->qb->where($this->qb->expr()->eq('booking.room', $room->getId()));
        }

        return $this->getEventsByQuery($this->qb);
    }

    /**
     *
     * @return Array
     */
    private function getSingleInstances()
    {
        $singleDateConditions = array();
        // case 1: event starts between selected dates
        $singleDateConditions[] = call_user_func_array(array(
            $this->qb->expr(),
            'andX',
        ), array(
            $this->qb->expr()->lte(':dateFrom', 'event.dtStart'),
            $this->qb->expr()->lte('event.dtStart', ':dateTo'),
        ));

        // case 2: event starts before selected dateFrom but ends after selected dateFrom
        $singleDateConditions[] = call_user_func_array(array(
            $this->qb->expr(),
            'andX',
        ), array(
            $this->qb->expr()->lt('event.dtStart', ':dateFrom'),
            $this->qb->expr()->lte(':dateFrom', 'event.dtEnd'),
        ));

        // or-combine the two cases for valid results
        $singleDateCondition = call_user_func_array(array(
            $this->qb->expr(),
            'orX',
        ), $singleDateConditions);

        $this->qb
            ->andWhere($this->qb->expr()->isNull('event.recurrenceRule'))
            ->andWhere($singleDateCondition)
            ->andWhere($this->qb->expr()->eq('booking.isActive', 1))
        ;
        /*$qb
            ->where($this->qb->expr()->isNull('event.recurrenceRule'))
            ->andWhere('(:dateFrom <= event.dtStart AND :dateTo >= event.dtEnd) OR (:dateFrom >= event.dtStart AND :dateFrom < event.dtEnd) OR (:dateTo > event.dtStart AND :dateTo <= event.dtEnd) OR (:dateFrom >= event.dtStart AND :dateTo <= event.dtEnd)')
            ->andWhere($this->qb->expr()->eq('booking.isActive', 1))
        ;*/

        return $this->qb->getQuery()->getResult();
    }

    /**
     *
     * @return Array
     */
    private function getRecurringInstances()
    {
        $events = array();

        $this->qb
            ->andWhere($this->qb->expr()->isNotNull('event.recurrenceRule'))
            ->andWhere($this->qb->expr()->eq('booking.isActive', 1))
        ;

        $recurringEvents = $this->qb->getQuery()->getResult();

        $eventUtil = new EventUtil($this->getEntityManager());
        foreach ($recurringEvents as $recurringEvent) {
            /* @var $recurringEvent \Xima\ICalBundle\Entity\Component\Event */
            $events = array_merge($events, $eventUtil->getInstances($recurringEvent, false, $this->qb->getParameter(':dateFrom')->getValue(), $this->qb->getParameter(':dateTo')->getValue()));
        }

        return $events;
    }

    /**
     * @param QueryBuilder $qb
     *
     * @return Array
     */
    protected function getEventsByQuery(QueryBuilder $qb)
    {
        $this->qb = $qb;

        $singles = $this->getSingleInstances();
        $recurrings = $this->getRecurringInstances();

        $events = array_merge($singles, $recurrings);

        return $events;
    }
}
