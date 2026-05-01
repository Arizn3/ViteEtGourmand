<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'label' => 'Email :',
                'required' => true
            ])
            ->add('prenom', null, [
                'label' => 'Prenom :',
                'required' => true
            ])
            ->add('nom', null, [
                'label' => 'Nom :',
                'required' => true
            ])
            ->add('telephone', null, [
                'label' => 'Mobile :',
                'required' => true
            ])
            ->add('adresse', null, [
                'label' => 'Adresse :',
                'required' => true
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
