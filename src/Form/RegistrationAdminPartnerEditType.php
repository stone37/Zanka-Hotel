<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RegistrationAdminPartnerEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, ['label' => 'Prénom'])
            ->add('lastname', TextType::class, ['label' => 'Nom'])
            ->add('email', EmailType::class, ['label' => 'Adresse e-mail'])
            ->add('phone', TextType::class, ['label' => 'Téléphone'])
            ->add('hostelNumber', IntegerType::class, ['label' => 'Nombre d\'établissement'])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr'  => ['class' => 'form-control md-textarea', 'rows'  => 5],
                'required' => false
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'attr'  => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Pays',
                'data' => 'CI',
                'required' => false
            ])
            ->add('city', TextType::class, ['label' => 'Ville', 'required' => false])
            ->add('isVerified', CheckboxType::class, ['label' => 'Activée']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['Default', 'Registration']
        ]);
    }
}
