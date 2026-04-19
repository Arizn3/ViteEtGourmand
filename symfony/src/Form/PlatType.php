<?php

namespace App\Form;

use App\Entity\Allergene;
use App\Entity\Plat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PlatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomPlat', null, [
                'label' => 'Nom du  plat :',
                'required' => true,
            ])
            ->add('photo', FileType::class, [
                // 'mapped' => false : Le traitement des images se fait manuellement dans EmployeController.php
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    // Contraintes utiliser pour les entrées de type File, avec le namespace ~/File
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Le fichier doit être une image (JPG ou PNG) et ne pas dépasser 2 Mo.'
                    ])
                ],
            ])
            ->add('allergenes', EntityType::class, [
                'class' => Allergene::class,
                'choice_label' => 'description',
                'label' => 'Allergène(s) :',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plat::class,
        ]);
    }
}
