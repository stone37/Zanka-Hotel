<?php

namespace App\Form\Filter;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NameFilterType extends AbstractFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'label' => 'Nom de l\'hébergement',
            'required' => false,
            'attr' => ['placeholder' => 'Ex: Residence Marine']
        ]);
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}