<?php

namespace App\Form;

use App\Entity\Taxe;
use App\Repository\TaxeRepository;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class TaxeChoiceType extends AbstractType
{
    private TaxeRepository $repository;
    private Security $security;

    public function __construct(TaxeRepository $repository, Security $security)
    {
        $this->repository = $repository;
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
        $resolver->setDefaults([
            'choices' => fn(Options $options): array => $this->repository->findBy(['owner' => $this->security->getUser(), 'enabled' => true], ['createdAt' => 'desc']),
            'choice_value' => 'id',
            'choice_label' => function (Taxe $taxe) {
                return $taxe->getName() .' ('.$taxe->getValue().' %)';
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
        return 'app_partner_taxe_choice';
    }
}