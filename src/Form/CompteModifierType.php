<?php

namespace App\Form;

use App\Entity\Compte;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class CompteModifierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->remove('num_adherent')
            ->remove('actif')
            ->remove('dateAdhesion')
            ->remove('archive')
            ->remove('dateDernierPaiement')
            ->remove('motifDesactivage')
            ->remove('agreeTerms')
            ->remove('is_adherent')

        ;
    }

    public function getParent(){
        return RegistrationFormType::class;
      }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Compte::class,
        ]);
    }
}
