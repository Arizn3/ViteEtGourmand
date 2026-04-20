<?php

namespace App\Form;

use App\Entity\Horaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HoraireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('heureOuverture', null, [
                'label' => 'Heure d\'ouverture',
            ])
            ->add('HeureFermeture', null, [
                'label' => 'Heure de fermeture'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Horaire::class,
        ]);
    }
}
