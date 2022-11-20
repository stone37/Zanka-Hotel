<?php

namespace App\Form;

use App\Data\BookingData;
use App\Form\DataTransformer\BookingLocationTransformer;
use App\Form\DataTransformer\DateStringToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class BookingDataType extends AbstractType
{
    private DateStringToArrayTransformer $stringToArrayTransformer;
    private BookingLocationTransformer $bookingLocationTransformer;

    public function __construct(
        DateStringToArrayTransformer $stringToArrayTransformer,
        BookingLocationTransformer $bookingLocationTransformer
    )
    {
        $this->stringToArrayTransformer = $stringToArrayTransformer;
        $this->bookingLocationTransformer = $bookingLocationTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('location', TextType::class, [
                'attr' => [
                    'class' => 'form-control booking_data_location',
                    'placeholder' => 'Où allez-vous ?'
                ]
            ])
            ->add('duration', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('adult', IntegerType::class, ['attr' => ['class' => 'booking_data_adult d-none']])
            ->add('children', IntegerType::class, ['attr' => ['class' => 'booking_data_children d-none']])
            ->add('roomNumber', IntegerType::class, ['attr' => ['class' => 'booking_data_roomNumber d-none']]);

        $builder->get('duration')->addModelTransformer($this->stringToArrayTransformer);
        $builder->get('location')->addModelTransformer($this->bookingLocationTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookingData::class
        ]);
    }
}


