<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use App\Entity\Commande;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('datePrestation', null, [
                'label' => 'Date de livraison souhaitée :',
                'required' => true,
                'attr' => [
                    'id' => 'commande_datePrestation'
                ]
            ])
            ->add('heureLivraison', null, [
                'label' => 'Heure de livraison souhaitée :',
                'required' => true,
                'attr' => [
                    'id' => 'commande_heureLivraison'
                ],
            ]);
        if ($options['modification'] === false) {
            $builder->add('adresseLivraison', null, [
                'label' => 'Adresse de livraison :',
                'required' => true,
                'attr' => [
                    'id' => 'commande_adresseLivraison'
                ]
            ]);
        };
        if ($options['modification'] === false) {
            $builder->add('villeLivraison', null, [
                'label' => 'Ville :',
                'required' => true,
                'attr' => [
                    'id' => 'commande_villeLivraison',
                ]
            ]);
        };
        if ($options['modification'] === false) {
            $builder->add('nbPersonne', null, [
                'label' => 'Nombre de boîte à repas :',
                'required' => true,
                'attr' => [
                    'id' => 'commande_nbPersonne',
                    'min' => 1,
                ]
            ]);
        };
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
            'modification' => false,
        ]);
    }
}
