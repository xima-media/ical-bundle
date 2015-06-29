<?php



namespace Xima\ICalBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Xima\ICalBundle\Entity\Property\Event\RecurrenceRule;

class RecurrenceRuleAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('freq', 'choice', array(
               'choices' => array(
                   RecurrenceRule::FREQ_DAILY => RecurrenceRule::FREQ_DAILY,
                   RecurrenceRule::FREQ_MONTHLY => RecurrenceRule::FREQ_MONTHLY,
                   RecurrenceRule::FREQ_WEEKLY => RecurrenceRule::FREQ_WEEKLY,
                   RecurrenceRule::FREQ_YEARLY => RecurrenceRule::FREQ_YEARLY,
               ), ))
            ->add('interval', 'integer', array('required' => false))
            ->add('count', 'integer', array('required' => false))
            ->add('byMonth')
            ->add('byWeekNo')
            ->add('byYearDay')
            ->add('byMonthDay')
            ->add('byDay')
            ->add('byHour', 'integer', array('required' => false))
            ->add('byMinute', 'integer', array('required' => false))
            ->add('bySecond', 'integer', array('required' => false))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
        ;
    }
}
