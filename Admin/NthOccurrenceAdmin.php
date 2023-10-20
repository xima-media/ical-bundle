<?php

namespace Xima\ICalBundle\Admin;

use Eluceo\iCal\Property\Event\RecurrenceRule;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class NthOccurrenceAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('nth', 'choice',
                [
                    'choices' =>
                        [
                            RecurrenceRule::NTH_OCCURRENCE_EVERY => 'choice.nth_occurrence.' . RecurrenceRule::NTH_OCCURRENCE_EVERY,
                            RecurrenceRule::NTH_OCCURRENCE_FIRST => 'choice.nth_occurrence.' . RecurrenceRule::NTH_OCCURRENCE_FIRST,
                            RecurrenceRule::NTH_OCCURRENCE_SECOND => 'choice.nth_occurrence.' . RecurrenceRule::NTH_OCCURRENCE_SECOND,
                            RecurrenceRule::NTH_OCCURRENCE_THIRD => 'choice.nth_occurrence.' . RecurrenceRule::NTH_OCCURRENCE_THIRD,
                            RecurrenceRule::NTH_OCCURRENCE_FOURTH => 'choice.nth_occurrence.' . RecurrenceRule::NTH_OCCURRENCE_FOURTH,
                            RecurrenceRule::NTH_OCCURRENCE_FIFTH => 'choice.nth_occurrence.' . RecurrenceRule::NTH_OCCURRENCE_FIFTH,
                            RecurrenceRule::NTH_OCCURRENCE_LAST => 'choice.nth_occurrence.' . RecurrenceRule::NTH_OCCURRENCE_LAST
                        ],
                    'choice_translation_domain' => 'XimaICalBundle'
                ]
            )
            ->add('occurrence', 'choice',
                [
                    'choices' =>
                        [
                            RecurrenceRule::WEEKDAY_MONDAY => RecurrenceRule::WEEKDAY_MONDAY,
                            RecurrenceRule::WEEKDAY_TUESDAY => RecurrenceRule::WEEKDAY_TUESDAY,
                            RecurrenceRule::WEEKDAY_WEDNESDAY => RecurrenceRule::WEEKDAY_WEDNESDAY,
                            RecurrenceRule::WEEKDAY_THURSDAY => RecurrenceRule::WEEKDAY_THURSDAY,
                            RecurrenceRule::WEEKDAY_FRIDAY => RecurrenceRule::WEEKDAY_FRIDAY,
                            RecurrenceRule::WEEKDAY_SATURDAY => RecurrenceRule::WEEKDAY_SATURDAY,
                            RecurrenceRule::WEEKDAY_SUNDAY => RecurrenceRule::WEEKDAY_SUNDAY
                        ],
                    'choice_translation_domain' => 'XimaICalBundle'
                ]
            );
    }
}
