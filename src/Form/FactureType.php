<?php

namespace App\Form;

use App\Entity\Facture;
use App\Entity\Commande;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numCommande', TextType::class, [
                'label' => 'Numéro de commande',
                'required' => true,
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('total', NumberType::class, [
                'label' => 'Total',
                'required' => true,
            ])
            ->add('commande', EntityType::class, [
                'class' => Commande::class,
                'choice_label' => 'id',
                'label' => 'Commande',
                'required' => false,
                'placeholder' => 'Sélectionner une commande',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Client',
                'required' => false,
                'placeholder' => 'Sélectionner un client',
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
