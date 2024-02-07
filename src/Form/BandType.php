<?php

namespace App\Form;

use App\Entity\Band;
use App\Entity\MusicCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'inputForm',
                ],
            ])
            ->add('logo', TextType::class, [
                'attr' => [
                    'class' => 'inputForm',
                ],
            ])
            ->add('define_style', TextType::class, [
                'attr' => [
                    'class' => 'inputForm',
                ],
            ])
            ->add('music_category', EntityType::class, [
                'class' => MusicCategory::class,
                'choice_label' => 'category',
                'attr' => [
                    'class' => 'inputForm',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Band::class,
        ]);
    }
}
