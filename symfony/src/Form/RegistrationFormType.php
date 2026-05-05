<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    private UrlGeneratorInterface $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $url = $this->router->generate('app_home_conditions_mention');

        $builder
            // Pour l'attribut email de l'Entité Utilisateur
            ->add('email', null, [
                'label' => 'Email :',
                'required' => true
            ])
            // Pour l'attribut prenom de l'Entité Utilisateur
            ->add('prenom', null, [
                'label' => 'Prenom :',
                'required' => true
            ])
            // Pour l'attribut nom de l'Entité Utilisateur
            ->add('nom', null, [
                'label' => 'Nom :',
                'required' => true
            ])
            // Pour l'attribut telephone de l'Entité Utilisateur
            ->add('telephone', null, [
                'label' => 'Telephone :',
                'required' => true
            ])
            // Pour l'attribut adresse de l'Entité Utilisateur
            ->add('adresse', null, [
                'label' => 'Mon adresse :',
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
                        'min' => 10,
                        'minMessage' => 'Le mot de passe doit contenir minimum {{ limit }} caractères',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
                        'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
                    ]),
                ],
            ])
            // Condition d'utilisation à valider par l'utilisateur
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => "J'accepte les <a href='$url' target='_blank'> conditions d'utilisations</a>",
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
