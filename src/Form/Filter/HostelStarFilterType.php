<?php

namespace App\Form\Filter;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class HostelStarFilterType extends AbstractFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [
                '1 étoile' => 1,
                '2 étoiles' => 2,
                '3 étoiles' => 3,
                '4 étoiles' => 4,
                '5 étoiles' => 5,
                'Non classé' => 0,
            ],
            'label' => 'Nombre d\'étoiles',
            'label_attr' => ['class' => 'star-label'],
            'required' => false,
            'multiple' => true,
            'expanded' => true,
            'choice_attr' => function($choice, $key, $value) {
                return ['class' => 'form-check-input filled-in'];
            },
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
