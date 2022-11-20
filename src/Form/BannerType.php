<?php

namespace App\Form;

use App\Entity\Banner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class BannerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('url', TextType::class, ['label' => 'URL (facultatif)', 'required' => false])
            ->add('mainText', TextType::class, ['label' => 'Texte principal (facultatif)', 'required' => false])
            ->add('secondaryText', TextType::class, ['label' => 'Texte secondaire (facultatif)', 'required' => false])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Texte' => Banner::TYPE_TEXT,
                    'Carousel' => Banner::TYPE_SILVER,
                ],
                'label' => 'Type',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Type'
            ])
            ->add('device', ChoiceType::class, [
                'choices' => [
                    'Tous les appareils' => Banner::DEVICE_ALL,
                    'Ordinateurs' => Banner::DEVICE_DESKTOP,
                    'Mobiles' => Banner::DEVICE_MOBILE
                ],
                'label' => 'Appareils ciblés',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Appareils ciblés'
            ])
            ->add('location', ChoiceType::class, [
                'choices' => [
                    'Top' => Banner::LOCATION_TOP,
                    'Middle' => Banner::LOCATION_MIDDLE,
                    'Bottom' => Banner::LOCATION_BOTTOM,
                ],
                'label' => 'Emplacement',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Emplacement'
            ])
            ->add('bgColor', TextType::class, ['label' => 'Couleur de fond (facultatif)', 'required' => false])
            ->add('enabled', CheckboxType::class, ['label' => 'Activée', 'required' => false])
            ->add('file', VichImageType::class, ['required' => false])
            ->add('fileMobile', VichImageType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Banner::class,
        ]);
    }
}
