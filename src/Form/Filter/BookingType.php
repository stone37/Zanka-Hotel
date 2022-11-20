<?php

namespace App\Form\Filter;

use App\Form\HostelPartnerChoiceType;
use App\Form\RoomPartnerChoiceType;
use App\Model\Admin\BookingSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hostel', HostelPartnerChoiceType::class, [
                'label' => 'Établissements',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'required' => false,
                'placeholder' => 'Établissements'
            ])
            ->add('room', RoomPartnerChoiceType::class, [
                'label' => 'Type d\'hébergement',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'required' => false,
                'placeholder' => 'Type d\'hébergement'
            ])
            ->add('code', TextType::class, ['label' => 'Code', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BookingSearch::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
