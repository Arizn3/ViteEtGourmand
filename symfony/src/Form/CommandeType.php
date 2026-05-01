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
                'required' => true
            ])
            ->add('heureLivraison', null, [
                'label' => 'Heure de livraison souhaitée :',
                'required' => true
            ])
            ->add('nbPersonne', null, [
                'label' => 'Nombre de boîte à repas :',
                'required' => true
            ])
            ->add('adresseLivraison', null, [
                'label' => 'Adresse de livraison :',
                'required' => true
            ])
            ->add('villeLivraison', null, [
                'label' => 'Ville :',
                'required' => true
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
