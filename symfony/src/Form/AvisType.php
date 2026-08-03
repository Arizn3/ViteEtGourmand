<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use App\Entity\Utilisateur;
use App\Entity\Avis;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', ChoiceType::class, [
                'choices' => [
                    '⭐️☆☆☆☆' => 1,
                    '⭐️⭐️☆☆☆' => 2,
                    '⭐️⭐️⭐️☆☆' => 3,
                    '⭐️⭐️⭐️⭐️☆' => 4,
                    '⭐️⭐️⭐️⭐️⭐️' => 5,
                ],
                'expanded' => true
            ])
            ->add('libelle', null,[
                'label' => 'Votre avis :',
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
