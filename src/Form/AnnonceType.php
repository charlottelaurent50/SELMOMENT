<?php

namespace App\Form;

use App\Entity\Annonce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('descriptif')
            ->add('date_publication')
            ->add('date_expiration')
            ->add('localisation')
            ->add('nb_cloches')
            ->add('archive')
            ->add('type', EntityType::class, array('class'=>'App\Entity\Type', 'choice_label'=>'libelle','label'=>'  '))
            ->add('categorie', EntityType::class, array('class'=>'App\Entity\Categorie', 'choice_label'=>'libelle','label'=>'  '))
            ->add('statut', EntityType::class, array('class'=>'App\Entity\Statut', 'choice_label'=>'libelle','label'=>'  '))
            ->add('compte')
            ->add('images', FileType::class, [
                'label' => false,
                'required' => false,
                'multiple' => true,
                'mapped'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
