<?php

namespace App\Form;

use App\Entity\BandInfo;
use App\Entity\band;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BandInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('country')
            ->add('region')
            ->add('department')
            ->add('city')
            ->add('email')
            ->add('phone')
            ->add('website')
            ->add('band', EntityType::class, [
                'class' => band::class,
'choice_label' => 'id',
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
