<?php

namespace App\Form;

use App\Entity\BandInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class BandInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('zipCode', TextType::class, [
            'attr' => [
                'class' => 'inputForm',
            ],
        ])
        ->add('city', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'inputForm hidden',
            ],
        ])
        ->add('department', TextType::class, [
            'attr' => [
                'class' => 'inputForm',
            ],
        ])
        ->add('region', TextType::class, [
            'attr' => [
                'class' => 'inputForm',
            ],
        ])
        ->add('country', TextType::class, [
            'attr' => [
                'class' => 'inputForm',
            ],
        ])
        ->add('email', EmailType::class, [
            'attr' => [
                'class' => 'inputForm',
            ],
        ])
        ->add('phone', TelType::class, [
            'attr' => [
                'class' => 'inputForm',
            ],
            'required' => false,
        ])
        ->add('website', TextType::class, [
            'attr' => [
                'class' => 'inputForm',
            ],
            'required' => false,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BandInfo::class,
        ]);
    }
}
