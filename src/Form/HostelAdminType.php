<?php

namespace App\Form;

use App\Entity\Hostel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HostelAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom de l\'établissement'])
            ->add('email', EmailType::class, ['label' => 'Adresse email'])
            ->add('category', CategoryChoiceType::class, [
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'label' => 'Type d\'établissement',
                'placeholder' => 'Type d\'établissement'
            ])
            ->add('starNumber', ChoiceType::class, [
                'choices' => [
                    'Non classé' => 0,
                    '1 étoile' => 1,
                    '2 étoiles' => 2,
                    '3 étoiles' => 3,
                    '4 étoiles' => 4,
                    '5 étoiles' => 5
                ],
                'label' => 'Nombre d\'étoiles',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Nombre d\'étoiles',
            ])
            ->add('address', TextType::class, ['label' => 'Adresse (facultatif)', 'required' => false])
            ->add('codePostal', TextType::class, ['label' => 'Code postal (facultatif)', 'required' => false])
            ->add('plan', PlanChoiceType::class, [
                'label' => 'Plan',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'required' => false,
                'placeholder' => 'Plan',
            ])
            ->add('enabled', CheckboxType::class, ['label' => 'Activé']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Hostel::class,
            'validation_groups' => ['Default', 'Admin']
        ]);
    }
}

