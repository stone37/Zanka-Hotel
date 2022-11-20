<?php

namespace App\Form\PromotionAction;

use App\Form\MoneyType;
use App\Util\LocaleCurrencyUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class FixedDiscountActionConfigurationType extends AbstractType
{
    private LocaleCurrencyUtil $util;
    private RequestStack $request;

    public function __construct(LocaleCurrencyUtil $util, RequestStack $request)
    {
        $this->util = $util;
        $this->request = $request;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', MoneyType::class, [
                'label' => false,
                'currency' => $this->util->getCurrencyCode($this->request->getMainRequest()->getLocale()),
                'scale' => 0
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'app_promotion_action_fixed_discount_configuration';
    }
}