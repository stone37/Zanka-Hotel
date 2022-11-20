<?php

namespace App\Form\Filter;

use App\Form\HostelPartnerChoiceType;
use App\Model\Admin\PayoutSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartnerPayoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hostel', HostelPartnerChoiceType::class, [
                'label' => 'Établissements',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Établissements',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PayoutSearch::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}