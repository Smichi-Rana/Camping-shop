<?php

namespace App\Form;

use App\Entity\Facture;
use App\Entity\Commande;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateFacture', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de la facture',
            ])
            ->add('montant', NumberType::class, [
                'label' => 'Montant',
            ])
            ->add('commande', EntityType::class, [
                'class' => Commande::class,
                'choice_label' => 'id',
                'label' => 'Commande',
                'placeholder' => 'Sélectionnez une commande',
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
            'data_class' => Facture::class,
        ]);
    }
}
