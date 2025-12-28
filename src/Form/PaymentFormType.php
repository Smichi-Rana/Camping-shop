<?php

namespace App\Form;

use App\Entity\Paiement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('methode', ChoiceType::class, [
                'label' => 'Mode de paiement',
                'choices' => [
                    'Carte bancaire' => 'cb',
                    'PayPal' => 'paypal',
                    'Virement bancaire' => 'virement',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('montant', MoneyType::class, [
                'label' => 'Montant à payer',
                'currency' => 'TND'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Paiement::class,
        ]);
    }
}
