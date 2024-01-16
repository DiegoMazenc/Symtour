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
                'class' => Band::class,
'choice_label' => 'name',
            ])
            ->add('role', EntityType::class, [
                'class' => RoleBand::class,
'choice_label' => 'role_name',
            ])
            ->add('profil', EntityType::class, [
                'class' => Profil::class,
'choice_label' => 'pseudo',
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
