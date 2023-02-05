<?php

namespace App\Form;

use App\Data\BookingData;
use App\Form\DataTransformer\DateStringToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingDataCheckType extends AbstractType
{
    public function __construct(private DateStringToArrayTransformer $stringToArrayTransformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('duration', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('adult', IntegerType::class, ['attr' => ['class' => 'booking_data_adult d-none']])
            ->add('children', IntegerType::class, ['attr' => ['class' => 'booking_data_children d-none']])
            ->add('roomNumber', IntegerType::class, ['attr' => ['class' => 'booking_data_roomNumber d-none']]);

        $builder->get('duration')->addModelTransformer($this->stringToArrayTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookingData::class
        ]);
    }
}


