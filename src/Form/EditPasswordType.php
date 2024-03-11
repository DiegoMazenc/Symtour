<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class EditPasswordType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('mdpActuel', PasswordType::class,[
            'required'=> true,
            'label' => false,
            'attr' => ['placeholder' => 'Ancien mot de passe'],
        ])
        ->add('verifNewPass', PasswordType::class,[
            'required'=> true,
            'label' => false,
            'attr' => ['placeholder' => 'Nouveau mot de passe'],
        ])
        ->add('confirmNewPass', PasswordType::class,[
            'required'=> true,
            'label' => false,
            'attr' => ['placeholder' => 'Confirmez le nouveau mot de passe'],
        ]);

    }
}