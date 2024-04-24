<?php

namespace App\Form;

use App\Entity\Hall;
use App\Entity\MusicCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class HallType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'inputForm',
                    'placeholder' => 'ex : l\'Olympia'
                ],
                'label' => 'Nom de la Salle *',
                
            ])
            ->add('logo',  FileType::class, [
                'attr' => [
                    'class' => 'inputForm',
                ],
                'label' =>'Logo',
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
        ->add('structure', TextType::class, [
            'attr' => [
                'class' => 'inputForm',
                'placeholder' => 'ex : Bar Live, Salle de concert, ...'
            ],
            'label' => 'Type de structure *',
            'constraints' => [
                new Length([
                    'min' => 3,
                    'minMessage' => 'Le type de structure doit comporter au moins {{ limit }} caractères.'
                ])
            ]
        ])
            ->add('music_category', EntityType::class, [
                'class' => MusicCategory::class,
                'required' => true,
                'choice_label' => 'category',
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'class' => 'inputForm',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Hall::class,
        ]);
    }
}
