<?php

namespace App\Form;

use App\Entity\Supplement;
use App\Repository\SupplementRepository;
use App\Util\LocaleCurrencyUtil;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class SupplementChoiceType extends AbstractType
{
    private SupplementRepository $repository;
    private LocaleCurrencyUtil $util;
    private RequestStack $request;
    private Security $security;

    public function __construct(
        SupplementRepository $repository,
        LocaleCurrencyUtil $util,
        RequestStack $request,
        Security $security
    )
    {
        $this->repository = $repository;
        $this->util = $util;
        $this->request = $request;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['multiple']) {
            $builder->addModelTransformer(new CollectionToArrayTransformer());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $supplements = $this->repository->findBy(['owner' => $this->security->getUser(), 'enabled' => true], ['position' => 'asc']);

        $resolver->setDefaults([
            'choices' => fn(Options $options): array => $supplements,
            'choice_value' => 'id',
            'choice_label' => function (Supplement $supplement) {
                return $supplement->getName() .' ('.$supplement->getPrice(). ' ' .
                    $this->util->getCurrencySymbol($this->request->getMainRequest()->getLocale()) .')';
            },
            'choice_translation_domain' => false
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'app_supplement_choice';
    }
}