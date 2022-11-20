<?php

namespace App\Form;

use App\Entity\Room;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipmentRoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('equipments', RoomEquipmentChoiceType::class, [
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'form-check-input filled-in'];
                },
                'label' => 'Sélectionner les équipements de l\'hébergement',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Room::class
        ]);
    }
}
