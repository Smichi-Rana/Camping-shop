<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],

            // 🔹 Informations personnelles
            ->add('first_name', TextType::class, [
                'label' => 'First Name',
                'attr'  => ['placeholder' => 'Enter your first name'],
            ])

            ->add('last_name', TextType::class, [
                'label' => 'Last Name',
                'attr'  => ['placeholder' => 'Enter your last name'],
            ])

            // 🔹 Contact
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr'  => ['placeholder' => 'email@example.com'],
            ])

            ->add('phone', TextType::class, [
                'label' => 'Phone Number',
                'attr'  => ['placeholder' => ' xx xxx xxx'],
            ])

            // 🔹 Adresse
            ->add('adresse', TextType::class, [
                'label' => 'Address',
                'attr'  => ['placeholder' => 'Your full address'],
            ])

            ->add('code_postal', TextType::class, [
                'label' => 'Postal Code',
                'attr'  => ['placeholder' => 'e.g. 1002'],
            ])

            // 🔹 Password
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Password',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'minMessage' => 'Password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])

            // 🔹 Terms & Conditions
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'I agree to the terms of service',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You must agree to our terms.',
                    ]),
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
