<?php

namespace App\Form;

use App\Entity\Annonce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, array('label'=>'  '))
            ->add('descriptif', TextType::class, array('label'=>'  '))
            ->add('date_expiration', DateType::class, array('label'=>'  '))
            ->add('pas_date_expiration', CheckboxType::class, [
                'label'=>'  ', 
                'required' => false, 
                'mapped' => false])
            ->add('localisation', TextType::class, array('label'=>'  '))
            ->add('nb_cloches', IntegerType::class, ['label'=>'  ', 'required'=>false])
            ->add('type', EntityType::class, array('class'=>'App\Entity\Type', 'choice_label'=>'libelle','label'=>'  '))
            ->add('categorie', EntityType::class, array('class'=>'App\Entity\Categorie', 'choice_label'=>'libelle','label'=>'  '))
            ->add('statut', EntityType::class, array('class'=>'App\Entity\Statut', 'choice_label'=>'libelle','label'=>'  '))
            ->add('domaine', EntityType::class, array('class'=>'App\Entity\Domaine', 'choice_label'=>'libelle','label'=>'  '))
            ->add('images', FileType::class, [
                'label' => false,
                'required' => false,
                'multiple' => true,
                'mapped'=>false
            ]) ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
