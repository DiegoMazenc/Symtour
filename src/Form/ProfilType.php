<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Profil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('country', TextType::class, [
                'attr' => [
                    'class' => 'inputForm',
                ],
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'class' => 'inputForm',
                ],
            ])
            ->add('zip_code', TextType::class, [
                'attr' => [
                    'class' => 'inputForm',
                ],
            ])
            ->add('description', TextType::class, [
                'attr' => [
                    'class' => 'inputForm',
                ],
            ])
            ->add('picture', FileType::class, [
                'attr' => [
                    'class' => 'inputForm',
                ],
                'label' =>'Photo',
                'mapped' => false,
                'required' =>false,
                'constraints' => [
                    new File([
                        'maxSize' => '2500k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'veuillez sélectione une image jpg, jpeg ou png'
                    ])
                ]
            ])
            ->add('pseudo', TextType::class, [
                'attr' => [
                    'class' => 'inputForm',
                ],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profil::class,
        ]);
    }
}
