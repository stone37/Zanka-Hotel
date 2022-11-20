<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('city', CityChoiceType::class, [
                'label' => 'Ville',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'ville'
            ])
            ->add('town', TextType::class, ['label' => 'Quartier ou commune (facultatif)'])
            ->add('detail', TextType::class, [
                'label' => 'Detail sur l\'emplacement (facultatif)',
                'attr' => ['placeholder' => 'Veuillez remplir l\'adresse détaillée, y compris le quartier, la rue, ...']
            ])
            ->add('latitude', TextType::class, ['label' => 'Latitude'])
            ->add('longitude', TextType::class, ['label' => 'Longitude']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}


