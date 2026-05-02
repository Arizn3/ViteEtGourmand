<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Pour l'attribut email de l'Entité Utilisateur
            ->add('email', null, [
                'required' => true
            ])
            // Pour l'attribut prenom de l'Entité Utilisateur
            ->add('prenom', null, [
                'required' => true
            ])
            // Pour l'attribut nom de l'Entité Utilisateur
            ->add('nom', null, [
                'required' => true
            ])
            // Pour l'attribut telephone de l'Entité Utilisateur
            ->add('telephone', null, [
                'required' => true
            ])
            // Pour l'attribut adresse de l'Entité Utilisateur
            ->add('adresse', null, [
                'required' => true
            ])
            // Propriété Password
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas',
                'first_options' => [
                    'label' => 'Mot de passe :',
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe :',
                ],
                'attr' => ['autocomplete' => 'new-password'],
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez choisir un mot de passe',
                    ]),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir minimum {{ limit }} caractères',
                    ]),
                ],
            ])
            // Condition d'utilisation à valider par l'utilisateur
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => "J'accepte les conditions d'utilisations",
                'label_html' => true,
                'constraints' => [
                    new IsTrue(
                        message: 'Vous devez accepter les conditions d\'utilisation',
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
