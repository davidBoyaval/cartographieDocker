<?php

namespace App\Form;

use App\Entity\Site;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('longitude')
            ->add('latitude')
            ->add('libelle')
            ->add('ville')
            ->add('numero')
            ->add('rue')
            ->add('cp')
            ->add('code_ape')
            ->add('ca')
            ->add('filiere')
            ->add('date')
            ->add('sujet')
            ->add('poste')
            ->add('save', SubmitType::class, 
                ['label' => 'Créer Entreprise'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Site::class,
        ]);
    }
}
