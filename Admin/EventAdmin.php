<?php

namespace Xima\ICalBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class EventAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('dtStart', 'sonata_type_datetime_picker')
            ->add('dtEnd', 'sonata_type_datetime_picker')
            ->add('recurrenceRule', 'sonata_type_admin', array(
                'required' => false,
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'admin_code' => 'xima.ical.admin.ical.recurrence_rule',
            ))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('dtStart')
            ->add('dtEnd')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
        ;
    }
}
