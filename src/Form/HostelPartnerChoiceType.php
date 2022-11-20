<?php

namespace App\Form;

use App\Repository\HostelRepository;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class HostelPartnerChoiceType extends AbstractType
{
    private HostelRepository $hostelRepository;
    private Security $security;

    public function __construct(HostelRepository $hostelRepository, Security $security)
    {
        $this->hostelRepository = $hostelRepository;
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
            'choices' => fn(Options $options): array => $this->hostelRepository->findBy([
                'owner' => $this->security->getUser()], ['position' => 'asc']
            ),
            'choice_value' => 'id',
            'choice_label' => 'name',
            'choice_translation_domain' => false,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'app_hostel_partner_choice';
    }
}
