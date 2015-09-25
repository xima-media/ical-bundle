<?php

namespace Xima\ICalBundle\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Xima\ICalBundle\Entity\Component\Event;
use Xima\ICalBundle\Event\FormEventSubscriber;

class EventAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('dtStart', 'sonata_type_datetime_picker')
            ->add('dtEnd', 'sonata_type_datetime_picker')
            ->add('allDayStart', 'sonata_type_date_picker')
            ->add('allDayEnd', 'sonata_type_date_picker')
            ->add('isAllDayEvent', 'checkbox')
            ->add(
                'recurrenceRule',
                'sonata_type_admin',
                array(
                    'required' => false,
                ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'admin_code' => 'xima_ical.sonata_admin.recurrence_rule',
                )
            )
        ;
    }
}
