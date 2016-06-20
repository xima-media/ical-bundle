<?php

namespace Xima\ICalBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Xima\ICalBundle\Entity\Property\Event\RecurrenceRule;

class RecurrenceRuleAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('freq', 'choice', array(
                'choices' => array(
                    '' => 'choose_recurrence',
                    RecurrenceRule::FREQ_DAILY => RecurrenceRule::FREQ_DAILY,
                    RecurrenceRule::FREQ_MONTHLY => RecurrenceRule::FREQ_MONTHLY,
                    RecurrenceRule::FREQ_WEEKLY => RecurrenceRule::FREQ_WEEKLY,
                    RecurrenceRule::FREQ_YEARLY => RecurrenceRule::FREQ_YEARLY,
                ), 'choice_translation_domain' => 'XimaICalBundle'))
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
                    'choices' => $this->getNumbersArray(52, 1)
                )
            )
            ->add('byYearDay', 'choice', array(
                    'multiple' => true,
                    'choices' => $this->getNumbersArray(365, 1)
                )
            )
            ->add('byMonthDay', 'choice', array(
                    'multiple' => true,
                    'choices' => $this->getNumbersArray(31, 1)
                )
            )
            ->add('byDays',
                'sonata_type_collection',
                array(
                    'required' => false,
                ),
                array(
                    'edit' => 'inline',
                    'admin_code' => 'xima_ical.sonata_admin.nth_occurrence',
                ))

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
