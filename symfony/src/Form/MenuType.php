<?php

namespace App\Form;

use App\Entity\Menu;
use App\Entity\Plat;
use App\Entity\Regime;
use App\Entity\Theme;
use DateTime;
use Doctrine\Common\Collections\Expr\Value;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // add() rajoute un champ en lien avec une propriété de l'Entité Menu
            ->add('titre', null, [
                'label' => 'Nom du menu :',
                'required' => true
            ])
            // EntityType::class permet d'inclure une liste déroulante de choix possibles qui sont des données en base
            // Theme::class représente l'Entité ciblée
            ->add('theme', EntityType::class, [
                'class' => Theme::class,
                'label' => 'Thème du menu :',
                // Choix d'affichage de la donnée en base pour les différents choix
                'choice_label' => 'description',
                'required' => true
            ])
            ->add('personneMini', null, [
                'label' => 'Minimum de personnes pour le menu :',
                'required' => true,
                'attr' => ['min' => 1]
            ])
            ->add('prixPersonne', null, [
                'label' => 'Prix par personne :',
                'required' => true,
                'attr' => ['min' => 1]
            ])
            ->add('description', null, [
                'label' => 'Ajouter une decription / condition :',
                'required' => true
            ])
            ->add('qttRestante', null, [
                'label' => 'Ajouter une quantité restante :',
                'required' => true,
                'attr' => ['min' => 1]
            ])
            ->add('regime', EntityType::class, [
                'class' => Regime::class,
                'label' => 'Régime :',
                'choice_label' => 'description',
                'required' => true
            ])
            ->add('plats', EntityType::class, [
                'class' => Plat::class,
                'label' => 'Choix des plats :',
                'choice_label' => 'nomPlat',
                'multiple' => true,
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
