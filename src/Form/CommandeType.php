<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateCommande', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de la commande',
            ])
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'En attente' => 'en_attente',
                    'Valide' => 'valide',
                    'Payée' => 'payée',
                    'Annulée' => 'annulee'
                ],
                'label' => 'Statut de la commande',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username', // adapte selon ton entité User
                'label' => 'Utilisateur',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
