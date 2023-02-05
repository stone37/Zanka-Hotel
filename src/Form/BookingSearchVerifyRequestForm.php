<?php

namespace App\Form;

use App\Data\BookingSearchVerifyRequestData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingSearchVerifyRequestForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'Entrer le code de vérification',
                'help' => 'Merci de consulter vos emails. Si vous n\'avez pas reçu de code de vérification, veuillez réessayer.',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookingSearchVerifyRequestData::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
