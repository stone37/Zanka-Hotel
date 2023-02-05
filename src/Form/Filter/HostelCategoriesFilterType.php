<?php

namespace App\Form\Filter;

use App\Context\HostelCategoryContext;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HostelCategoriesFilterType extends AbstractFilterType
{
    private HostelCategoryContext $hostelCategoryContext;

    public function __construct(HostelCategoryContext $hostelCategoryContext) {
        $this->hostelCategoryContext = $hostelCategoryContext;
    }

    /*public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [];

        foreach ($this->hostelCategoryContext->getCategories() as $category) {
            $choices[$category->getName()] = $category->getId();
        }

        $builder->add('categories', ChoiceType::class, [
            'choices' => $choices,
            'label' => 'Type d\'établissement',
            'label_attr' => ['class' => 'type-label'],
            'required' => false,
            'multiple' => true,
            'expanded' => true,
            'choice_attr' => function($choice, $key, $value) {
                return ['class' => 'form-check-input filled-in'];
            },
        ]);
    }*/

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $choices = [];

        foreach ($this->hostelCategoryContext->getCategories() as $category) {
            $choices[$category->getName()] = $category->getId();
        }

        $resolver->setDefaults([
            'choices' => $choices,
            'label' => 'Type d\'établissement',
            'required' => false,
            'expanded' => true,
            'multiple' => false,
            'choice_attr' => ['class' => 'form-check-input with-gap'],
            'label_attr' => ['class' => 'type-label'],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
