<?php

namespace App\Form\Filter;

use App\Form\HostelPartnerChoiceType;
use App\Model\Admin\RoomSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PartnerRoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hostel', HostelPartnerChoiceType::class, [
                'label' => 'Établissements',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Établissements',
                'required' => false
            ])
            ->add('name', TextType::class, ['label' => 'Nom', 'required' => false])
            ->add('enabled', CheckboxType::class, ['label' => 'Activé', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RoomSearch::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
