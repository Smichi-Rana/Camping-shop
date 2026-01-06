<?php

namespace App\Form;

use App\Entity\Paiement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaiementFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant', MoneyType::class, [
                'label' => 'Montant',
                'currency' => 'EUR',
                'attr' => ['class' => 'form-control']
            ])
            ->add('methodePaiement', ChoiceType::class, [
                'label' => 'Méthode de paiement',
                'choices' => [
                    'Carte bancaire' => 'carte_bancaire',
                    'PayPal' => 'paypal',
                    'Virement bancaire' => 'virement',
                    'Espèces' => 'especes',
                    'Chèque' => 'cheque',
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('nomClient', TextType::class, [
                'label' => 'Nom du client',
                'attr' => ['class' => 'form-control']
            ])
            ->add('emailClient', EmailType::class, [
                'label' => 'Email du client',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Description de la transaction...'
                ]
            ])
            ->add('devise', ChoiceType::class, [
                'label' => 'Devise',
                'choices' => [
                    'Euro (EUR)' => 'EUR',
                    'Dollar US (USD)' => 'USD',
                    'Livre Sterling (GBP)' => 'GBP',
                    'Dinar Tunisien (TND)' => 'TND',
                ],
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Paiement::class,
        ]);
    }
}
