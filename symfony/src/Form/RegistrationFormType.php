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

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Pour l'attribut email de l'Entité Utilisateur
            ->add('email')
            // Pour l'attribut prenom de l'Entité Utilisateur
            ->add('prenom')
            // Pour l'attribut nom de l'Entité Utilisateur
            ->add('nom')
            // Pour l'attribut telephone de l'Entité Utilisateur
            ->add('telephone')
            // Pour l'attribut adresse de l'Entité Utilisateur
            ->add('adresse')
            // Propriété Password
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez choisir un mot de passe',
                    ),
                    new Length(
                        min: 6,
                        minMessage: 'Le mot de passe doit contenir minimum {{ limit }} caractères',
                        // longueur maximale autorisée par Symfony pour des raisons de sécurité
                        max: 4096,
                    ),
                ],
            ])
            // Condition d'utilisation à valider par l'utilisateur
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => "J'accepte les <a href='/condition-utilisation'>conditions d'utilisations</a>",
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
