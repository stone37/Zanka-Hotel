<?php

namespace App\Form;

use App\Calculator\FixedDiscountPriceCalculator;
use App\Calculator\PercentageDiscountPriceCalculator;
use App\Entity\PromotionAction;
use App\Form\PromotionAction\FixedDiscountActionConfigurationType;
use App\Form\PromotionAction\PercentageDiscountActionConfigurationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PromotionActionType extends AbstractType
{
    private array $actionTypes = [
        'Pourcentage de remise' => 'percentage_discount',
        'Prix fixe de remise' => 'fixed_discount'
    ];

    private array $actionConfigurationTypes = [
        'percentage_discount' => PercentageDiscountActionConfigurationType::class,
        'fixed_discount' => FixedDiscountActionConfigurationType::class
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $defaultActionType = current($this->actionTypes);
        $defaultActionConfigurationType = $this->actionConfigurationTypes[$defaultActionType];

        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => $this->actionTypes,
                'attr' => ['class' => 'mdb-select md-form md-outline dropdown-primary app-promotion-action-type']
            ])
            ->add('configuration', $defaultActionConfigurationType, ['label' => false]);

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event): void {
                $this->addConfigurationTypeToForm($event);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event): void {
                /** @var array|null $data */
                $data = $event->getData();

                if ($data === null) {
                    return;
                }

                $form = $event->getForm();
                $formData = $form->getData();

                if ($formData !== null) {
                    $formData->setType($data['type']);
                    $formData->setConfiguration($data['configuration']);

                    if ($data['type'] === FixedDiscountPriceCalculator::TYPE) {
                        if ($data['configuration']['amount'] === '') {
                            return;
                        }
                    }

                    if ($data['type'] === PercentageDiscountPriceCalculator::TYPE) {
                        if ($data['configuration']['amount'] === '') {
                            return;
                        }
                    }

                    $form->setData($formData);
                }

                $actionConfigurationType = $this->actionConfigurationTypes[$data['type']];
                $form->add('configuration', $actionConfigurationType, [
                    'label' => false
                ]);
            })
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_promotion_action';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PromotionAction::class,
        ]);
    }

    private function addConfigurationTypeToForm(FormEvent $event): void
    {
        /** @var PromotionAction|null $data */
        $data = $event->getData();

        if ($data === null) {
            return;
        }

        $form = $event->getForm();

        $actionConfigurationType = $this->actionConfigurationTypes[$data->getType()];
        $form->add('configuration', $actionConfigurationType, [
            'label' => false,
        ]);
    }
}


