<?php

namespace App\Form\Filter;

use App\Context\HostelEquipmentContext;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class HostelEquipmentsFilterType extends AbstractFilterType
{
    private HostelEquipmentContext $hostelEquipmentContext;

    public function __construct(HostelEquipmentContext $hostelEquipmentContext) {
        $this->hostelEquipmentContext = $hostelEquipmentContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [];

        foreach ($this->hostelEquipmentContext->getEquipments() as $equipment) {
            $choices[$equipment->getName()] = $equipment->getId();
        }

        $builder->add('equipments', ChoiceType::class, [
            'choices' => $choices,
            'label' => 'Services et équipements',
            'label_attr' => ['class' => 'equipment-label'],
            'required' => false,
            'multiple' => true,
            'expanded' => true,
            'choice_attr' => function($choice, $key, $value) {
                return ['class' => 'form-check-input filled-in'];
            },
        ]);
    }
}
