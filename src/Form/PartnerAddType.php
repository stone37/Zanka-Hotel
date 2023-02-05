<?php

namespace App\Form;

use App\Entity\User;
use App\Form\DataTransformer\IntegerToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PartnerAddType extends AbstractType
{
    public function __construct(private IntegerToStringTransformer $transformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $passwordAttrs = ['minlength' => 8, 'maxlength' => 4096];

        $builder
            ->add('firstname', TextType::class, ['label' => 'Prénom'])
            ->add('lastname', TextType::class, ['label' => 'Nom'])
            ->add('email', EmailType::class, ['label' => 'Adresse e-mail'])
            ->add('phone', IntegerType::class, [
                'label' => 'Téléphone',
                'help_html' => true,
                'help' => 'Ex: <span>225</span>0790909090, (<span>225</span>) est l\'indicatif.'
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe']),
                    new Length([
                        'min' => 8,
                        'max' => 4096,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères'
                    ]),
                ],
                'first_options' => ['label' => 'Mot de passe', 'attr' => $passwordAttrs],
                'second_options' => ['label' => 'Confirmer le mot de passe', 'attr' => $passwordAttrs],
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'attr'  => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Pays',
                'data' => 'CI',
                'required' => false
            ])
            ->add('city', TextType::class, ['label' => 'Ville', 'required' => false]);

        $builder->get('phone')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['Default', 'Registration']
        ]);
    }
}
