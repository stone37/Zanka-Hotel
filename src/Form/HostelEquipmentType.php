<?php

namespace App\Form;

use App\Entity\Hostel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HostelEquipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('equipments', EquipmentChoiceType::class, [
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'form-check-input filled-in'];
                },
                'label' => 'Sélectionner les équipements de l\'établissement',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Hostel::class
        ]);
    }
}
