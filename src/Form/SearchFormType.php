<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search', TextType::class, [
                'label' => 'Rechercher un utilisateur',
                'attr' => ['placeholder' => 'Pseudo, email,...'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
            ]);
    }
}