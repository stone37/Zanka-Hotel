<?php

namespace App\Form\Filter;

use App\Model\HostelSearch;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HostelType extends AbstractType
{
    private CategoryRepository $repository;

    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', ChoiceType::class, [
                'choices' => $this->repository->getEnabledData(),
                'label' => 'Type de propriété',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'required' => false,
                'placeholder' => 'Type de propriété',
            ])
            ->add('name', TextType::class, ['label' => 'Nom de l\'établissement', 'required' => false])
            ->add('star', ChoiceType::class, [
                'choices' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    'Non classé' => '0',
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Nombre d’étoiles',
                'required' => false,
            ])
            ->add('offer', CheckboxType::class, ['label' => 'Offres', 'required' => false])
            ->add('priceMin',IntegerType::class, [
                'label' => 'Min',
                'required' => false,
                'attr' => ['placeholder' => 'De']
            ])
            ->add('priceMax',IntegerType::class, [
                'label' => 'Max',
                'required' => false,
                'attr' => ['placeholder' => 'à']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => HostelSearch::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}



