<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\ProfilRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function __construct( private ProfilRepository $profil)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            // ->add('roles')
            ->add('password', RepeatedType::class )
            ->add('status', ChoiceType::class, [
                "required" => true,
                "expanded" => true,
                "multiple" => false,

                // Liaison avec le construct
                // 'choice_loader' => new CallbackChoiceLoader(function (): array{
                //     return $this->profil->findAll();
                // })

                // Ajouter des choix
                // 'choices' => [
                //     "Sélectionner" => "",
                //     "active" => "active",
                //     "inactive" => "inactive",
                //     "dead" => "dead",

                // ]
                ])

                // Ajouter un champ qui n'est pas Mappé
                ->add("firstname", null,[
                    "mapped" => false,
                ])
                ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
