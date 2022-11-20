<?php

namespace App\Form;

use App\Entity\TimeInterval;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimeIntervalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start', TimeType::class, ['label' => 'À partir de', 'widget' => 'single_text'])
            ->add('end', TimeType::class, ['label' => 'Jusqu\'à', 'widget' => 'single_text']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TimeInterval::class,
        ]);
    }
}
