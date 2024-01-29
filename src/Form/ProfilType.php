<?php

namespace App\Form;

use App\Entity\Profil;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('country')
            ->add('city')
            ->add('zip_code')
            ->add('description')
            ->add('picture', FileType::class, [
                'label' =>'Photo',
                'mapped' => false,
                'required' =>false,
                'constraints' => [
                    new File([
                        'maxSize' => '2500k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'veuillez sélectione une image jpg, jpeg ou png'
                    ])
                ]
            ])
            ->add('pseudo')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profil::class,
        ]);
    }
}
