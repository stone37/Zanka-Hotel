<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre (facultatif)',
                'attr' => ['placeholder' => 'Donner titre à votre commentaire'],
                'required' => false
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Votre commentaire (facultatif)',
                'attr'  => ['class' => 'form-control md-textarea', 'rows'  => 4],
                'required' => false
            ])
            ->add('personalRating', NumberType::class, [
                'label' => 'Noter le personnel',
                'attr' => ['min' => 0, 'max' => 10, 'placeholder' => 'Donner une note allant de 0 à 10.'],
                'data' => null
            ])
            ->add('equipmentRating', NumberType::class, [
                'label' => 'Noter les équipements et installation',
                'attr' => ['min' => 0, 'max' => 10, 'placeholder' => 'Donner une note allant de 0 à 10.'],
                'data' => null
            ])
            ->add('propertyRating', NumberType::class, [
                'label' => 'Noter la propriété',
                'attr' => ['min' => 0, 'max' => 10, 'placeholder' => 'Donner une note allant de 0 à 10.'],
                'data' => null
            ])
            ->add('comfortRating', NumberType::class, [
                'label' => 'Noter le confort',
                'attr' => ['min' => 0, 'max' => 10, 'placeholder' => 'Donner une note allant de 0 à 10.'],
                'data' => null
            ])
            ->add('priceRating', NumberType::class, [
                'label' => 'Noter le rapport qualité/prix',
                'attr' => ['min' => 0, 'max' => 10, 'placeholder' => 'Donner une note allant de 0 à 10.'],
                'data' => null
            ])
            ->add('locationRating', NumberType::class, [
                'label' => 'Noter la situation géographique',
                'attr' => ['min' => 0, 'max' => 10, 'placeholder' => 'Donner une note allant de 0 à 10.'],
                'data' => null
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}

