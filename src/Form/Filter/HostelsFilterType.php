<?php

namespace App\Form\Filter;

use App\Model\HostelSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HostelsFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', NameFilterType::class)
            ->add('equipments', HostelEquipmentsFilterType::class, ['required' => false, 'label' => false])
            ->add('price', PriceFilterType::class, ['required' => false, 'label' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HostelSearch::class,
            'csrf_protection' => false,
            'method' => 'GET'
            /*'allow_extra_fields' => true,*/
        ]);
    }

    public function getBlockPrefix(): ?string
    {
        return '';
    }
}
