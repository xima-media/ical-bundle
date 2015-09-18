<?php
namespace Xima\ICalBundle\EventListener\Entity\Component;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs ;
use Doctrine\Common\EventSubscriber;
use Xima\ICalBundle\Entity\Component\Event;

class EventEntitySubscriber implements EventSubscriber
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
     * @param PreUpdateEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        $entities = $uow->getScheduledEntityUpdates();

        foreach ($entities as $entity) {
            if (!($entity instanceof Event)) {
                continue;
            }

            /** @var $entity \Xima\ICalBundle\Entity\Component\Event */
            $changeSet = $uow->getEntityChangeSet($entity);

            if (isset($changeSet['dtStart'])) {
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