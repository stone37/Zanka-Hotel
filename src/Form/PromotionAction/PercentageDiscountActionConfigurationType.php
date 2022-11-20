<?php

namespace App\Form\PromotionAction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class PercentageDiscountActionConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', PercentType::class, [
                'label' => false,
                'symbol' => '%',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le montant de la remise en pourcentage doit être un nombre et ne peut pas être vide.'
                    ]),
                    new Range([
                        'min' => 0,
                        'max' => 1,
                        'notInRangeMessage' => 'Le montant de la remise en pourcentage doit être compris entre 0% et 100%.',
                    ]),
                ]
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_promotion_action_percentage_discount_configuration';
    }
}