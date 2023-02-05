<?php

namespace App\Form\Filter;

use App\Context\RoomEquipmentContext;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class RoomEquipmentsFilterType extends AbstractFilterType
{
    public function __construct(private RoomEquipmentContext $roomEquipmentContext)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [];

        foreach ($this->roomEquipmentContext->getEquipments() as $equipment) {
            $choices[$equipment->getName()] = $equipment->getId();
        }

        $builder->add('roomEquipments', ChoiceType::class, [
            'choices' => $choices,
            'label' => 'Services et équipements de chambre',
            'label_attr' => ['class' => 'room-equipment-label'],
            'required' => false,
            'multiple' => true,
            'expanded' => true,
            'choice_attr' => function($choice, $key, $value) {
                return ['class' => 'form-check-input filled-in'];
            },
        ]);
    }
}
