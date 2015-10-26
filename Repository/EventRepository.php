<?php

namespace Xima\ICalBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Xima\ICalBundle\Util\EventUtil;

class EventRepository extends EntityRepository
{
    /**
     * @var QueryBuilder
     */
    protected $qb;

    /**
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     *
     * @return Array
     */
    public function getEventsByDate(\DateTime $dateFrom, \DateTime $dateTo)
    {
        $this->qb = (!is_null($this->qb)) ? $this->qb : $this->getEntityManager()->createQueryBuilder()->select('event')->from($this->getEntityName(), 'event');

        $singles = $this->getSingleInstances(clone $this->qb, $dateFrom, $dateTo);
        $recurrings = $this->getRecurringInstances(clone $this->qb, $dateFrom, $dateTo);

        $events = array_merge($singles, $recurrings);

        return $events;
    }

    /**
     *
     * @return Array
     */
    private function getSingleInstances(QueryBuilder $qb, \DateTime $dateFrom, \DateTime $dateTo)
    {
        $singleDateConditions = array();
        // case 1: event starts between selected dates
        $singleDateConditions[] = call_user_func_array(array(
            $qb->expr(),
            'andX',
        ), array(
            $qb->expr()->lte(':dateFrom', $qb->getRootAliases()[0].'.dtStart'),
            $qb->expr()->lte($qb->getRootAliases()[0].'.dtStart', ':dateTo'),
        ));

        // case 2: event starts before selected dateFrom but ends after selected dateFrom
        $singleDateConditions[] = call_user_func_array(array(
            $qb->expr(),
            'andX',
        ), array(
            $qb->expr()->lt($qb->getRootAliases()[0].'.dtStart', ':dateFrom'),
            $qb->expr()->lte(':dateFrom', $qb->getRootAliases()[0].'.dtEnd'),
        ));

        // or-combine the two cases for valid results
        $singleDateCondition = call_user_func_array(array(
            $qb->expr(),
            'orX',
        ), $singleDateConditions);

        $qb
            ->andWhere($qb->expr()->isNull($qb->getRootAliases()[0].'.recurrenceRule'))
            ->andWhere($singleDateCondition)
        ;

        $qb->setParameter('dateFrom', $dateFrom);
        $qb->setParameter('dateTo', $dateTo);

        return $qb->getQuery()->getResult();
    }

    /**
     *
     * @return Array
     */
    private function getRecurringInstances(QueryBuilder $qb, \DateTime $dateFrom, \DateTime $dateTo)
    {
        $events = array();

        $qb->andWhere($qb->expr()->isNotNull($qb->getRootAliases()[0].'.recurrenceRule'));

        $recurringEvents = $qb->getQuery()->getResult();
        $eventUtil = new EventUtil($this->getEntityManager());
        foreach ($recurringEvents as $recurringEvent) {
            /* @var $recurringEvent \Xima\ICalBundle\Entity\Component\Event */
            $events = array_merge($events, $eventUtil->getInstances($recurringEvent, $dateFrom, $dateTo));
        }

        return $events;
    }
}
