<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de la réclamation',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description détaillée',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5
                ]
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Service client' => 'service_client',
                    'Produit défectueux' => 'produit_defectueux',
                    'Livraison' => 'livraison',
                    'Facturation' => 'facturation',
                    'Autre' => 'autre',
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('nomUtilisateur', TextType::class, [
                'label' => 'Votre nom',
                'attr' => ['class' => 'form-control']
            ])
            ->add('emailUtilisateur', EmailType::class, [
                'label' => 'Votre email',
                'attr' => ['class' => 'form-control']
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En attente' => 'en_attente',
                    'En cours' => 'en_cours',
                    'Résolue' => 'resolue',
                    'Rejetée' => 'rejetee',
                ],
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('reponse', TextareaType::class, [
                'label' => 'Réponse (Admin)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
