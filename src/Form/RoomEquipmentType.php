<?php

namespace App\Form;

use App\Entity\RoomEquipment;
use App\Entity\RoomEquipmentGroup;
use App\Repository\RoomEquipmentGroupRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomEquipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('description', TextareaType::class, [
                'label' => 'Description (facultatif)',
                'attr'  => ['class' => 'form-control md-textarea', 'rows'  => 4],
                'required' => false
            ])
            ->add('roomEquipmentGroup', EntityType::class, [
                'class' => RoomEquipmentGroup::class,
                'choice_label' => 'name',
                'query_builder' => function (RoomEquipmentGroupRepository $er) {
                    return $er->getEnabled();
                },
                'label' => 'Groupe',
                'required' => false,
                'attr' => [
                    'class' => 'mdb-select md-outline md-form dropdown-primary',
                ],
                'placeholder' => 'Groupe',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RoomEquipment::class,
        ]);
    }
}
