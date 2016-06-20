<?php

namespace Xima\ICalBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Xima\ICalBundle\Event\FormEventSubscriber;

class EventAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('dateFrom', 'sonata_type_date_picker')
            ->add('timeFrom')
            ->add('dateTo', 'sonata_type_date_picker')
            ->add('timeTo')
            ->add('noTime', 'checkbox', array(
                'required' => false,
            ))
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
