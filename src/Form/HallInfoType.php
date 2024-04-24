<?php

namespace App\Form;

use App\Entity\Hall;
use App\Entity\HallInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class HallInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
        ->add('zipCode', TextType::class, [
            'attr' => [
                'class' => 'inputForm',
            ],
            'label' => 'Code Postal *',

        ])
        ->add('city', TextType::class, [
            'label' => false,
            

        ])
        ->add('nbrStreet', TextType::class, [
            'label' => 'N°',
            'attr' => [
                'class' => 'inputForm',
            ],
        ])
        ->add('street', TextType::class, [
            'attr' => [
                'class' => 'inputForm',
            ],
            'label' => 'Rue *',

        ])
        ->add('department', TextType::class, [
            'attr' => [
                'class' => 'inputForm',
            ],
            'label' => 'Département *',

        ])
        ->add('region', TextType::class, [
            'attr' => [
                'class' => 'inputForm',
            ],
            'label' => 'Région *',

        ])
        ->add('country', TextType::class, [
            'attr' => [
                'class' => 'inputForm',
            ],
            'label' => 'Pays *',

        ])
        ->add('email', EmailType::class, [
            'attr' => [
                'class' => 'inputForm',
            ],
            'label' => 'E-mail *',

        ])
        ->add('phone', TelType::class, [
            'attr' => [
                'class' => 'inputForm',
            ],
            'label' => 'Téléphone',

            'required' => false,
        ])
        ->add('website', TextType::class, [
            'attr' => [
                'class' => 'inputForm',
            ],
            'label' => 'Site Internet',

            'required' => false,
        ])
        ;
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HallInfo::class,
        ]);
    }
}
