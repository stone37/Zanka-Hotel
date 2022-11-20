<?php

namespace App\Form\Filter;

use App\Form\CategoryChoiceType;
use App\Model\Admin\HostelAdminSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminHostelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', CategoryChoiceType::class, [
                'label' => 'Type de propriété',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'required' => false,
                'placeholder' => 'Type de propriété',
            ])
            ->add('name', TextType::class, ['label' => 'Nom', 'required' => false])
            ->add('email', EmailType::class, ['label' => 'Email', 'required' => false])
            ->add('enabled', CheckboxType::class, ['label' => 'Activé', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => HostelAdminSearch::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
