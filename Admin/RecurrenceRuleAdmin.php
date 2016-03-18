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
                ),))
            ->add('until', 'sonata_type_datetime_picker')
            ->add('count', 'integer', array(
                'required' => false,
                'attr' => array('min' => 0)
            ))
            ->add('interval', 'integer', array(
                'required' => false,
                'attr' => array('min' => 0)
            ))
            ->add('byMonth', 'choice', array(
                    'multiple' => true,
                    'choices' => $this->getNumbersArray(12, 1)
                )
            )
            ->add('byWeekNo', 'choice', array(
                    'multiple' => true,
                    'choices' => $this->getNumbersArray(53, 1)
                )
            )
            ->add('byYearDay', 'choice', array(
                    'multiple' => true,
                    'choices' => $this->getNumbersArray(366, 1)
                )
            )
            ->add('byMonthDay', 'choice', array(
                    'multiple' => true,
                    'choices' => $this->getNumbersArray(31, 1)
                )
            )
            ->add('byDay', 'choice', array(
                    'multiple' => true,
                    'choices' => array(
                        RecurrenceRule::WEEKDAY_MONDAY => RecurrenceRule::WEEKDAY_MONDAY,
                        RecurrenceRule::WEEKDAY_TUESDAY => RecurrenceRule::WEEKDAY_TUESDAY,
                        RecurrenceRule::WEEKDAY_WEDNESDAY => RecurrenceRule::WEEKDAY_WEDNESDAY,
                        RecurrenceRule::WEEKDAY_THURSDAY => RecurrenceRule::WEEKDAY_THURSDAY,
                        RecurrenceRule::WEEKDAY_FRIDAY => RecurrenceRule::WEEKDAY_FRIDAY,
                        RecurrenceRule::WEEKDAY_SATURDAY => RecurrenceRule::WEEKDAY_SATURDAY,
                        RecurrenceRule::WEEKDAY_SUNDAY => RecurrenceRule::WEEKDAY_SUNDAY,),)
            )
            ->add('byHour', 'choice', array(
                    'multiple' => true,
                    'choices' => $this->getNumbersArray(59),
                    'required' => 'false'
                )
            )
            ->add('byMinute', 'choice', array(
                    'multiple' => true,
                    'choices' => $this->getNumbersArray(59),
                    'required' => 'false'
                )
            )
            ->add('bySecond', 'choice', array(
                    'multiple' => true,
                    'choices' => $this->getNumbersArray(59),
                    'required' => 'false'
                )
            )
            ;
    }

    private function getNumbersArray($number, $start = 0)
    {
        $numbers = array();
        for ($i = $start; $i <= $number; $i++) {
            $numbers[$i] = $i;
        }
        return $numbers;

    }
}
