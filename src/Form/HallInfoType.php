<?php

namespace App\Form;

use App\Entity\HallInfo;
use App\Entity\hall;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HallInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('country')
            ->add('region')
            ->add('department')
            ->add('city')
            ->add('zip_code')
            ->add('email')
            ->add('phone')
            ->add('website')
            ->add('hall', EntityType::class, [
                'class' => hall::class,
'choice_label' => 'id',
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
