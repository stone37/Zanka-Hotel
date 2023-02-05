<?php

namespace App\Form\Filter;

use App\Context\StorageBasedCurrencyContext;
use App\Entity\Settings;
use App\Form\MoneyType;
use App\Manager\SettingsManager;
use App\PropertyNameResolver\PriceNameResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Type;

class PriceFilterType extends AbstractFilterType
{
    private PriceNameResolver $priceNameResolver;
    private StorageBasedCurrencyContext $currencyContext;
    private ?Settings $settings;

    public function __construct(
        PriceNameResolver $priceNameResolver,
        StorageBasedCurrencyContext $currencyContext,
        SettingsManager $settingsManager
    )
    {
        $this->priceNameResolver = $priceNameResolver;
        $this->currencyContext = $currencyContext;
        $this->settings = $settingsManager->get();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add($this->priceNameResolver->resolveMinPriceName(), IntegerType::class, [
                'label' => 'Prix min',
                'required' => false,
                'attr' => ['class' => 'hostel_filter_price_min'],
                'constraints' => [
                    new Type([
                        'type' => 'numeric',
                        'message' => 'Le prix minimum doit être un prix valide',
                    ]),
                    new PositiveOrZero([
                        'message' => 'Le prix minimum ne peut pas être négatif',
                    ]),
                ],
            ])
            ->add($this->priceNameResolver->resolveMaxPriceName(), IntegerType::class, [
                'label' => 'Prix max',
                'required' => false,
                'attr' => ['class' => 'hostel_filter_price_max'],
                'constraints' => [
                    new Type([
                        'type' => 'numeric',
                        'message' => 'Le prix maximum doit être un prix valide',
                    ]),
                    new PositiveOrZero([
                        'message' => 'Le prix maximum ne peut pas être négatif',
                    ]),
                ],
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                if (!empty($event->getData())) {
                    $data = [];
                    foreach ($event->getData() as $key => $item) {
                        $data[$key] = trim($item);
                    }
                    $event->setData($data);
                }
            });
    }
}