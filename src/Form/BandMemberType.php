<?php

namespace App\Form;

use App\Entity\BandMember;
use App\Entity\band;
use App\Entity\profil;
use App\Entity\roleband;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BandMemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('band', EntityType::class, [
                'class' => band::class,
'choice_label' => 'id',
            ])
            ->add('role', EntityType::class, [
                'class' => roleband::class,
'choice_label' => 'id',
            ])
            ->add('profil', EntityType::class, [
                'class' => profil::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BandMember::class,
        ]);
    }
}
