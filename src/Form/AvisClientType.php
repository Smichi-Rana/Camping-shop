<?php

namespace App\Form;

use App\Entity\AvisClient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvisClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('commentaire', TextareaType::class, [
                'label' => 'Votre avis',
                'attr' => ['placeholder' => 'Écrivez votre commentaire...']
            ])
            ->add('note', null, [   // Symfony va gérer le type automatiquement
                'label' => false,
                'attr' => ['hidden' => true] // ce champ est caché
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AvisClient::class,
        ]);
    }
}
