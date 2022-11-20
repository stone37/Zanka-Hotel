<?php

namespace App\Form;

use App\Entity\Bedding;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BeddingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', ChoiceType::class, [
                'choices' => [
                    'Lit simple (largeur: 90-130 cm)' => 'Lit simple (largeur: 90-130 cm)',
                    'Lit double (largeur: 131-150 cm)' => 'Lit double (largeur: 131-150 cm)',
                    'Lit King-Size (largeur: 151-180 cm)' => 'Lit King-Size (largeur: 151-180 cm)',
                    'Grand lit King-Size (largeur: 181-210 cm)' => 'Grand lit King-Size (largeur: 181-210 cm)',
                    'Lit rond (dimensions variables)' => 'Lit rond (dimensions variables)',
                    'Lit d\'eau (dimensions variables)' => 'Lit d\'eau (dimensions variables)',
                    'Lit superposé (dimensions variables)' => 'Lit superposé (dimensions variables)',
                    'Canapé-lit (dimensions variables)' => 'Canapé-lit (dimensions variables)',
                    'Futon (dimensions variables)' => 'Futon (dimensions variables)'
                ],
                'label' => false,
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                //'placeholder' => 'Types de lits disponibles dans cet hébergement'
            ])
            ->add('number',ChoiceType::class, [
                'choices' => [
                    '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8,
                    '9' => 9, '10' => 10, '11' => 11, '12' => 12, '13' => 13, '14' => 14, '15' => 15
                ],
                'label' => false,
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Sélectionnez le nombres de lits'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bedding::class,
        ]);
    }
}
