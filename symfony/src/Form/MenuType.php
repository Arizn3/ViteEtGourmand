<?php

namespace App\Form;

use App\Entity\Menu;
use App\Entity\Plat;
use App\Entity\Regime;
use App\Entity\Theme;
use App\Repository\PlatRepository;
use App\Repository\RegimeRepository;
use App\Repository\ThemeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

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
                'required' => true,
                'query_builder' => function (ThemeRepository $tr) {
                    return $tr->createQueryBuilder('t')
                        ->andWhere('t.deletedAt IS NULL');
                }
            ])
            ->add('personneMini', null, [
                'label' => 'Minimum de personnes pour le menu :',
                'required' => true,
                'attr' => ['min' => 1]
            ])
            ->add('prixPersonne', NumberType::class, [
                'label' => 'Prix par personne :',
                'required' => true,
                'scale' => 2,
                'html5' => true,
                'attr' => [
                    'min' => 1,
                    'step' => 0.10
                ]
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
                'required' => true,
                'query_builder' => function (RegimeRepository $rr) {
                    return $rr->createQueryBuilder('r')
                        ->andWhere('r.deletedAt IS NULL');
                }
            ])
            ->add('plats', EntityType::class, [
                'class' => Plat::class,
                'label' => 'Choix des plats (minimum 3) :',
                'choice_label' => 'nomPlat',
                'expanded' => true,
                'multiple' => true,
                'required' => true,
                'row_attr' => ['class' => 'listePlats'],
                'constraints' => [
                    new Count([
                        'min' => 3,
                        'minMessage' => 'Veuillez sélectionner au moins 3 plats',
                    ]),
                    new Count([
                        'max' => 3,
                        'maxMessage' => 'Veuillez sélectionner uniquement 3 plats',
                    ])
                ],
                'query_builder' => function (PlatRepository $pr) {
                    return $pr->createQueryBuilder('p')
                        ->andWhere('p.deletedAt IS NULL');
                }
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
