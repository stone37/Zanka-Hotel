<?php

namespace App\Form;

use App\Entity\Room;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Chambre' => 'Chambre',
                    'Suite' => 'Suite',
                    'Chambre Simple' => 'Chambre Simple',
                    'Chambre Double' => 'Chambre Double',
                    'Chambre Triple' => 'Chambre Triple',
                    'Chambre Quadruple' => 'Chambre Quadruple',
                    'Chambre Lits Jumeaux' => 'Chambre Lits Jumeaux',
                    'Chambre Lits Jumeaux ou Double' => 'Chambre Lits Jumeaux ou Double',
                    'Chambre Familiale' => 'Chambre Familiale',
                    'Studio' => 'Studio',
                    'Appartement' => 'Appartement',
                    'Villa' => 'Villa',
                    'Dortoir' => 'Dortoir'
                ],
                'label' => 'Type d\'hébergement',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary app-room-type'],
                'placeholder' => 'Type d\'hébergement'
            ])
            ->add('specification', ChoiceType::class, [
                'choices' => [
                    'Standard' => 'Standard',
                    'Deluxe' => 'Deluxe',
                    'Supérieur' => 'Supérieur',
                    'Exécutive' => 'Exécutive',
                    'Familiale' => 'Familiale',
                    'Présidentielle' => 'Présidentielle',
                    'Premier' => 'Premier',
                    'Entreprise' => 'Entreprise',
                    'Confort' => 'Confort',
                    'Romantique' => 'Romantique',
                    'Classique' => 'Classique',
                    'Royal' => 'Royal',
                    'Elite' => 'Elite',
                    'A la mode' => 'A la mode'
                ],
                'label' => 'Spécification (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary app-room-specification'],
                'placeholder' => 'Spécification (facultatif)',
                'required' => false
            ])
            ->add('feature', ChoiceType::class, [
                'choices' => [
                    'Vue sur Mer' => 'Vue sur Mer',
                    'Vue sur Montage' => 'Vue sur Montage',
                    'Vue sur Lac' => 'Vue sur Lac',
                    'Vue sur Lagune' => 'Vue sur Lagune',
                    'Vue sur Fleuve' => 'Vue sur Fleuve',
                    'Vue sur Jardin' => 'Vue sur Jardin',
                    'Vue sur Parc' => 'Vue sur Parc',
                    'Vue sur Piscine' => 'Vue sur Piscine',
                    'Vue sur Ville' => 'Vue sur Ville',
                    'A deux niveaux' => 'A deux niveaux',
                    'Style occidental' => 'Style occidental',
                    'Style chinois' => 'Style chinois',
                    'Style japonais' => 'Style japonais',
                    'Accessible' => 'Accessible',
                    'Accessible aux Personnes à Mobilité Réduite' => 'Accessible aux Personnes à Mobilité Réduite',
                ],
                'label' => 'Caractéristiques (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary app-room-feature'],
                'placeholder' => 'Caractéristiques (facultatif)',
                'required' => false
            ])
            ->add('amenities', ChoiceType::class, [
                'choices' => [
                    'Avec Balcon' => 'Avec Balcon',
                    'Avec Baignoire' => 'Avec Baignoire',
                    'Avec Sauna' => 'Avec Sauna',
                    'Avec Douche' => 'Avec Douche',
                    'Avec Salle de Bains' => 'Avec Salle de Bains',
                    'Avec Salle de Bains communes' => 'Avec Salle de Bains communes',
                    'Avec Salle de Bains Extérieur Privative' => 'Avec Salle de Bains Extérieur Privative',
                    'Avec Salle de Bains Privative' => 'Avec Salle de Bains Privative',
                    'Avec Terrasse' => 'Avec Terrasse',
                    'Avec Toilettes Communes' => 'Avec Toilettes Communes',
                    'Avec Toilettes et Douche Communes' => 'Avec Toilettes et Douche Communes',
                    'Sans fenêtre' => 'Sans fenêtre'
                ],
                'label' => 'Commodités (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary app-room-amenities'],
                'placeholder' => 'Commodités (facultatif)',
                'required' => false
            ])
            ->add('perfectName', TextType::class, [
                'label' => 'Nom de personnalisé (facultatif)',
                'help' => 'Vous pouvez personnaliser le nom de l\'hébergement si vous le souhaitez.',
                'required' => false
            ])
            ->add('smoker', ChoiceType::class, [
                'choices' => [
                    'Non-fumeurs' => 1,
                    'Fumeurs' => 2,
                    'Cet hébergement est fumeurs et non-fumeurs' => 3
                ],
                'label' => 'Fumeurs ou non-fumeurs',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary']
            ])
            ->add('description', CKEditorType::class, [
                'label' => false,
                'config' => ['height' => '100', 'uiColor' => '#ffffff', 'toolbar' => 'basic'],
                'required' => false
            ])
            ->add('roomNumber', IntegerType::class, [
                'label' => 'Nombre d\'hébergements (de ce type)'
            ])
            ->add('price', IntegerType::class, [
                'label' => false,
                'attr' => ['min' => 0, 'placeholder' => '0']
            ])
            ->add('occupant', IntegerType::class, [
                'label' => 'Nombre d\'occupant max.',
                'help' => 'Indiquez le nombre de personnes maximum pouvant dormir dans cet hébergement.'
            ])
            ->add('area', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['min' => 0, 'placeholder' => '0']
            ])
            ->add('beddings', CollectionType::class, [
                'entry_type' => BeddingType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false
            ])
            ->add('taxes',  TaxeChoiceType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'mdb-select md-outline md-form dropdown-primary',
                    'data-label-select-all' => 'Tout sélectionnée'
                ],
                'label_attr' => ['class' => 'ml-1'],
                'multiple' => true,
                'required' => false
            ])
            ->add('dataRoomNumber', ChoiceType::class, [
                'choices' => [
                    '0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4,
                    '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9
                ],
                'label' => 'Nombre de chambres',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Nombre de chambres',
                'data' => 1
            ])
            ->add('dataLivingRoomNumber', ChoiceType::class, [
                'choices' => [
                    '0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5
                ],
                'label' => 'Nombre de salons',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Nombre de salons',
                'data' => 0
            ])
            ->add('dataBathroomNumber', ChoiceType::class, [
                'choices' => [
                    '0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5,
                    '6' => 6, '7' => 7, '8' => 8, '9' => 9
                ],
                'label' => 'Nombre de salles de bains',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Nombre de salles de bains',
                'data' => 1
            ])
            ->add('enabled', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Metre l\'hébergement en ligne ?',
                'expanded' => true,
                'multiple' => false,
                'required' => false
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            /** @var Room $room */
            $room = $event->getData();
            $form = $event->getForm();

            $disabled = null !== $room->getId();

            $form->add('hostel', HostelPartnerChoiceType::class, [
                'label' => 'Établissements',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Établissements',
                'disabled' => $disabled
            ]);
        });

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}
