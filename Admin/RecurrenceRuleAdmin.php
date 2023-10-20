<?php

namespace Xima\ICalBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Xima\ICalBundle\Entity\Property\Event\RecurrenceRule;

class RecurrenceRuleAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('freq', 'choice',
                [
                    'choices' =>
                        [
                            '' => 'choose_recurrence',
                            RecurrenceRule::FREQ_DAILY => RecurrenceRule::FREQ_DAILY,
                            RecurrenceRule::FREQ_WEEKLY => RecurrenceRule::FREQ_WEEKLY,
                            RecurrenceRule::FREQ_MONTHLY => RecurrenceRule::FREQ_MONTHLY,
                            RecurrenceRule::FREQ_YEARLY => RecurrenceRule::FREQ_YEARLY
                        ],
                    'choice_translation_domain' => 'XimaICalBundle'
                ]
            )
            ->add('until', 'sonata_type_datetime_picker')
            ->add('count', 'integer', ['required' => false, 'attr' => ['min' => 0]])
            ->add('interval', 'integer', ['required' => false, 'attr' => ['min' => 0]])
            ->add('byMonth', 'choice', ['multiple' => true, 'choices' => $this->getNumbersArray(12, 1)]
            )
            ->add('byWeekNo', 'choice', ['multiple' => true, 'choices' => $this->getNumbersArray(52, 1)]
            )
            ->add('byYearDay', 'choice', ['multiple' => true, 'choices' => $this->getNumbersArray(365, 1)]
            )
            ->add('byMonthDay', 'choice', ['multiple' => true, 'choices' => $this->getNumbersArray(31, 1)]
            )
            ->add('byDays',
                'sonata_type_collection',
                ['required' => false],
                ['edit' => 'inline', 'admin_code' => 'xima_ical.sonata_admin.nth_occurrence'])
            ->add('byHour', 'choice', ['multiple' => true, 'choices' => $this->getNumbersArray(59), 'required' => 'false']
            )
            ->add('byMinute', 'choice', ['multiple' => true, 'choices' => $this->getNumbersArray(59), 'required' => 'false']
            )
            ->add('bySecond', 'choice', ['multiple' => true, 'choices' => $this->getNumbersArray(59), 'required' => 'false']
            );
    }

    private function getNumbersArray($number, $start = 0)
    {
        $numbers = [];
        for ($i = $start; $i <= $number; $i++) {
            $numbers[$i] = $i;
        }
        return $numbers;

    }
}
