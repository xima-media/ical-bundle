<?php
namespace Xima\ICalBundle\Event;

use Doctrine\ORM\Event\OnFlushEventArgs ;
use Xima\ICalBundle\Entity\Component\Event;
use Xima\ICalBundle\Util\EventUtil;

class DoctrineEventSubscriber implements \Doctrine\Common\EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'onFlush',
        );
    }

    /**
     * Update event's exclude dates and recurrenceIds when start datetime changes.
     *
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        $entities = array_merge($uow->getScheduledEntityUpdates(), $uow->getScheduledEntityInsertions());
        $eventUtil = new EventUtil($em);

        foreach ($entities as $entity) {
            if (!($entity instanceof Event)) {
                continue;
            }

            $eventUtil->cleanUpEvent($entity);
            //merge dates and times, apply noTime setting
            $entity->setDtStart($entity->getDateFrom());
            if ($entity->getTimeFrom() != null)
            {
                $entity->getDtStart()->setTime($entity->getTimeFrom()->format('H'), $entity->getTimeFrom()->format('i'));
            } else {
                $entity->getDtStart()->setTime(0,0);
                $entity->setTimeFrom(new \DateTime('1970-01-01'));
            }

            $entity->setDtEnd(clone($entity->getDateTo()));

            if ($entity->getTimeTo() != null)
            {
                $entity->getDtEnd()->setTime($entity->getTimeTo()->format('H'), $entity->getTimeTo()->format('i'));
            } else {
                $entity->getDtEnd()->setTime(0,0);
                $entity->setTimeTo(new \DateTime('1970-01-01'));
            }

            if ($entity->isNoTime()) {
                $entity->getDtEnd()->add(new \DateInterval('P1D'));
            }

            $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(get_class($entity)), $entity);

            /** @var $entity \Xima\ICalBundle\Entity\Component\Event */
            $changeSet = $uow->getEntityChangeSet($entity);

            if (isset($changeSet['dtStart']) && isset($changeSet['dtStart'][0])) {
                $interval = $changeSet['dtStart'][0]->diff($entity->getDtStart());
                foreach ($entity->getExDates() as $dateTime) {
                    $dateTime->add($interval);
                }

                // if event is a recurring, find all events that replace an instance
                $q = $em->createQuery("select e from Xima\\ICalBundle\\Entity\\Component\\Event e where e.uniqueId = '" . $entity->getUniqueId() . "' AND e.recurrenceId IS NOT NULL");
                $events = $q->getResult();

                foreach ($events as $event) {
                    /** @var $event \Xima\ICalBundle\Entity\Component\Event */
                    $recurrenceId = $event->getRecurrenceId();
                    $recurrenceId->getDatetime()->add($interval);
                    // make Doctrine accept a DateTime as changed
                    $recurrenceId->setDatetime(clone $recurrenceId->getDatetime());
                    $em->persist($recurrenceId);
                    $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(get_class($recurrenceId)), $recurrenceId);
                }
            }
        }
    }
}