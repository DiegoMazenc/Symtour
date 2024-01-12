<?php

namespace App\Form;

use App\Entity\HallMember;
use App\Entity\Profil;
use App\Entity\RoleHall;
use App\Entity\hall;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HallMemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('hall', EntityType::class, [
                'class' => Hall::class,
'choice_label' => 'id',
            ])
            ->add('role', EntityType::class, [
                'class' => RoleHall::class,
'choice_label' => 'id',
            ])
            ->add('profile', EntityType::class, [
                'class' => Profil::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HallMember::class,
        ]);
    }
}
