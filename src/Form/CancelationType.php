<?php

namespace App\Form;

use App\Entity\Cancelation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CancelationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('state', ChoiceType::class, [
                'choices' => [
                    'Jusqu\'à la date d\'arrivée (18H00)' => Cancelation::CANCEL_STATE_DAY,
                    '1 jour' => Cancelation::CANCEL_STATE_ONE_DAY,
                    '2 jours' => Cancelation::CANCEL_STATE_TWO_DAY,
                    '3 jours' => Cancelation::CANCEL_STATE_THREE_DAY,
                    '7 jours' => Cancelation::CANCEL_STATE_SEVEN_DAY,
                    '14 jours' => Cancelation::CANCEL_STATE_FOURTEEN_DAY
                ],
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'label' => false,
                //'placeholder' => 'Combien de jours à l’avance les clients peuvent-ils annuler sans frais ?',
            ])
            ->add('result', ChoiceType::class, [
                'choices' => [
                    'de la première nuit' => Cancelation::CANCEL_RESULT_FIRST,
                    'de la totalité du séjour' => Cancelation::CANCEL_RESULT_SECOND,
                ],
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'label' => false,
                //'placeholder' => 'ou les clients paieront 100 %',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cancelation::class,
        ]);
    }
}
