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
                    '' => 'Choose a recurrence',
                    RecurrenceRule::FREQ_DAILY => RecurrenceRule::FREQ_DAILY,
                    RecurrenceRule::FREQ_MONTHLY => RecurrenceRule::FREQ_MONTHLY,
                    RecurrenceRule::FREQ_WEEKLY => RecurrenceRule::FREQ_WEEKLY,
                    RecurrenceRule::FREQ_YEARLY => RecurrenceRule::FREQ_YEARLY,
                ), ))
            ->add('until', 'sonata_type_datetime_picker')
            ->add('count', 'integer', array('required' => false))
            ->add('interval', 'integer', array('required' => false))
            ->add('byMonth')
            ->add('byWeekNo')
            ->add('byYearDay')
            ->add('byMonthDay')
            ->add('byDay', 'choice', array(
                    'choices' => array(
                        '' => 'Choose a day',
                        RecurrenceRule::WEEKDAY_MONDAY => RecurrenceRule::WEEKDAY_MONDAY,
                        RecurrenceRule::WEEKDAY_TUESDAY => RecurrenceRule::WEEKDAY_TUESDAY,
                        RecurrenceRule::WEEKDAY_WEDNESDAY => RecurrenceRule::WEEKDAY_WEDNESDAY,
                        RecurrenceRule::WEEKDAY_THURSDAY => RecurrenceRule::WEEKDAY_THURSDAY,
                        RecurrenceRule::WEEKDAY_FRIDAY => RecurrenceRule::WEEKDAY_FRIDAY,
                        RecurrenceRule::WEEKDAY_SATURDAY => RecurrenceRule::WEEKDAY_SATURDAY,
                        RecurrenceRule::WEEKDAY_SUNDAY => RecurrenceRule::WEEKDAY_SUNDAY, ), )
            )
            ->add('byHour', 'integer', array('required' => false))
            ->add('byMinute', 'integer', array('required' => false))
            ->add('bySecond', 'integer', array('required' => false))
        ;
    }
}
