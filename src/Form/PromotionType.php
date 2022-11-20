<?php

namespace App\Form;

use App\Entity\Promotion;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('description', CKEditorType::class, [
                'label' => false,
                'config' => ['height' => '150', 'uiColor' => '#ffffff', 'toolbar' => 'basic']
            ])
            ->add('startDate', DateTimeType::class, [
                'label' => false,
                'widget' => 'single_text',
                'with_seconds' => false,
                'required' => false
            ])
            ->add('endDate', DateTimeType::class, [
                'label' => false,
                'widget' => 'single_text',
                'with_seconds' => false,
                'required' => false
            ])
            ->add('enabled', CheckboxType::class, ['label' => 'Activée', 'required' => false])
            ->add('rooms', RoomPartnerChoiceType::class, [
                'label' => 'Hébergement',
                'required' => false,
                'multiple' => true,
                'placeholder' => 'Hébergement',
                'attr' => [
                    'class' => 'mdb-select md-form md-outline dropdown-primary',
                    'data-label-select-all' => 'Tout sélectionnée'
                ],
                'label_attr' => ['class' => 'ml-1']
            ])
            ->add('action', PromotionActionType::class, [
                'label' => 'Action',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Promotion::class,
        ]);
    }
}
