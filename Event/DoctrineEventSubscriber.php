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
            /** @var $entity \Xima\ICalBundle\Entity\Component\Event */
            $eventUtil->cleanUpEvent($entity);
            $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(get_class($entity)), $entity);
            // apply changed $dtStart values to child events $recurrenceIds if any
            $changeSet = $uow->getEntityChangeSet($entity);
            if (isset($changeSet['dtStart']) && isset($changeSet['dtStart'][0])) {
                $interval = $changeSet['dtStart'][0]->diff($entity->getDtStart());
                $q = $em->createQuery("select e from Xima\\ICalBundle\\Entity\\Component\\Event e where e.uniqueId = '" . $entity->getUniqueId() . "' AND e.recurrenceId IS NOT NULL");
                $detachedEvents = $q->getResult();

                foreach ($detachedEvents as $detachedEvent) {
                    /** @var $detachedEvent \Xima\ICalBundle\Entity\Component\Event */
                    $recurrenceId = $detachedEvent->getRecurrenceId();
                    $recurrenceId->getDatetime()->add($interval);
                    // make Doctrine accept a DateTime as changed
                    $recurrenceId->setDatetime(clone $recurrenceId->getDatetime());
                    $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(get_class($recurrenceId)), $recurrenceId);
                }
            }
        }
    }
}