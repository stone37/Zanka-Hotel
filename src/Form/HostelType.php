<?php

namespace App\Form;

use App\Entity\Hostel;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HostelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'établissement',
                'help' => 'C\'est le nom que les clients verront lorsqu\'ils rechercheront un hébergement'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'help' => 'Nous vous communiquerons des informations importantes comme vos réservations au moyen de ce courriel'
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'help' => 'Nous vous communiquerons des informations importantes comme vos réservations au moyen de ce numéro de téléphone'
            ])
            ->add('description', CKEditorType::class, [
                'label' => false,
                'config' => ['height' => '100', 'uiColor' => '#ffffff', 'toolbar' => 'basic'],
                'required' => false
            ])
            ->add('parking', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Y a t\'il un parking dans ou près de l\'établissement ?',
                'expanded' => true,
                'multiple' => false
            ])
            ->add('breakfast', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Le petit-déjeuner est-il proposé ?',
                'expanded' => true,
                'multiple' => false
            ])
            ->add('children', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Des enfants peuvent-ils séjourner dans l\'établissement ? ',
                'expanded' => true,
                'multiple' => false
            ])
            ->add('wifi', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Le wifi est-il disponible dans l\'établissement ? ',
                'expanded' => true,
                'multiple' => false
            ])
            ->add('spokenLanguages', ChoiceType::class, [
                'choices' => [
                    'Français' => 'Français',
                    'Anglais' => 'Anglais',
                    'Espagnol' => 'Espagnol',
                    'Allemand' => 'Allemand',
                    'Arabe' => 'Arabe',
                ],
                'label' => 'Quelles langues parle le personnel ?',
                'required' => false,
                'multiple' => true,
                'placeholder' => 'Quelles langues parle le personnel ?',
                'attr' => [
                    'class' => 'mdb-select md-form md-outline dropdown-primary',
                    'data-label-select-all' => 'Tout sélectionnée'
                ],
                'label_attr' => ['class' => 'ml-1'],
                'data' => ['Français']
            ])
            ->add('animalsAllowed', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Votre établissement accepte-t-il les animaux domestiques ?',
                'expanded' => true,
                'multiple' => false
            ])
            ->add('mobilePaymentAllowed', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Accepter des paiements mobile dans l\'établissement ?',
                'expanded' => true,
                'multiple' => false,
                'required' => false
            ])
            ->add('cardPaymentAllowed', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Pouvez-vous débiter des cartes bancaire dans l\'établissement ?',
                'expanded' => true,
                'multiple' => false,
                'required' => false,
            ])
            ->add('closed', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Voulez vous fermer l\'établissement temporairement ?',
                'expanded' => true,
                'multiple' => false
            ])
            ->add('checkinTime', TimeIntervalType::class, ['label' => 'Horaires d\'arrivée'])
            ->add('checkoutTime', TimeIntervalType::class, ['label' => 'Horaires de départ'])
            ->add('address', TextType::class, ['label' => 'Adresse (facultatif)', 'required' => false])
            ->add('codePostal', TextType::class, ['label' => 'Code postal (facultatif)', 'required' => false])
            ->add('location', LocationType::class)
            ->add('cancellationPolicy', CancelationType::class);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            /** @var Hostel $hostel */
            $hostel = $event->getData();
            $form = $event->getForm();

            $disabled = null !== $hostel->getId();

            $form->add('category', CategoryChoiceType::class, [
                'label' => 'Type de propriété',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Type de propriété',
                'disabled' => $disabled,
            ])
            ->add('starNumber', ChoiceType::class, [
                'choices' => [
                    'Non classé' => 0,
                    '1 étoile' => 1,
                    '2 étoiles' => 2,
                    '3 étoiles' => 3,
                    '4 étoiles' => 4,
                    '5 étoiles' => 5
                ],
                'label' => 'Nombre d\'étoiles',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Nombre d\'étoiles',
                'data' => $disabled ? $hostel->getStarNumber() : 0
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Hostel::class
        ]);
    }
}
