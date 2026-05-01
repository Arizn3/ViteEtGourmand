<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                ]
            ])
            ->add('nbPersonne', null, [
                'label' => 'Nombre de boîte à repas :',
                'required' => true,
                'attr' => [
                    'id' => 'commande_nbPersonne'
                ]
            ])
            ->add('adresseLivraison', null, [
                'label' => 'Adresse de livraison :',
                'required' => true,
                'attr' => [
                    'id' => 'commande_adresseLivraison'
                ]
            ])
            ->add('villeLivraison', null, [
                'label' => 'Ville :',
                'required' => true,
                'attr' => [
                    'id' => 'commande_villeLivraison'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
