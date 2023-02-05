<?php

namespace App\Form;

use App\Data\BookingData;
use App\Entity\User;
use App\Form\DataTransformer\IntegerToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class BookingType extends AbstractType
{
    public function __construct(
        private Security $security,
        private IntegerToStringTransformer $transformer
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('occupants', CollectionType::class, [
                'entry_type' => OccupantType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Veuillez écrire vos demandes (facultatif)',
                'attr'  => ['class' => 'form-control md-textarea', 'rows'  => 4],
                'required' => false
            ]);

        /** @var ?User $user */
        $user = $this->security->getUser();

        if (!$user || empty($user->getFirstname())) {
            $builder->add('firstname', TextType::class, ['label' => 'Prénom']);
        }

        if (!$user || empty($user->getLastname())) {
            $builder->add('lastname', TextType::class, ['label' => 'Nom']);
        }

        if (!$user || empty($user->getEmail())) {
            $builder->add('email', EmailType::class, ['label' => 'Adresse e-mail']);
        }

        if (!$user || empty($user->getPhone())) {
            $builder->add('phone', IntegerType::class, [
                'label' => 'Numéro de téléphone',
                'help_html' => true,
                'help' => 'Ex: <span>225</span>0790909090, (<span>225</span>) est l\'indicatif.'
            ]);
        }

        if (!$user || empty($user->getCountry())) {
            $builder->add('country', CountryType::class, [
                'label' => 'Pays (facultatif)',
                'attr'  => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Pays',
                'data' => 'CI',
                'required' => false,
            ]);
        }

        if (!$user || empty($user->getCity())) {
            $builder->add('city', TextType::class, ['label' => 'Ville (facultatif)', 'required' => false]);
        }

        $builder->get('phone')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookingData::class,
            'validation_groups' => ['booking']
        ]);
    }
}
