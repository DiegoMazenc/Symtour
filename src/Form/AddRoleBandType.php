<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AddRoleBandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('role', null, [
                'required' => true,

            ])
            ->add('profilFind', null, [
                'required' => true,

            ])
            ->add('band', null, [
                'required' => true,

            ])
            
            ->add('submit', SubmitType::class, [
                'label' => 'Inviter',
            ]);
    }
}
