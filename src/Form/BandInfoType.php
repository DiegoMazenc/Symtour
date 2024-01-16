<?php

namespace App\Form;

use App\Entity\BandInfo;
use App\Entity\Band;
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
            ->add('bandId', EntityType::class, [
                'class' => Band::class,
'choice_label' => 'name',
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
