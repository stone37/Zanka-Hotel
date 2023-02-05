<?php

namespace App\Form\Filter;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HostelAverageRatingFilterType extends AbstractFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [
                'Exceptionnel' => 9.5,
                'Fabuleux' => 9,
                'Très bien' => 8,
                'Bien' => 7
            ],
            'label' => 'Note des voyageurs',
            'required' => false,
            'expanded' => true,
            'multiple' => false,
            'choice_attr' => ['class' => 'form-check-input with-gap'],
            'label_attr' => ['class' => 'rating-label'],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
