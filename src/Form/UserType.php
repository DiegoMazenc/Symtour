<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\TextType;
use App\Repository\ProfilRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;

class UserType extends AbstractType
{
    public function __construct(private ProfilRepository $profil)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                "required" => true,
            ])

            // ->add('roles')
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['placeholder' => 'mot de passe']], // Correction ici : Remplacement de "mot depasse" par "mot de passe"
                'required' => true,
                'first_options' => [
                    'label' => false,
                    'attr' => ['placeholder' => 'Mot de passe'], // Déplacement de 'attr' ici pour spécifier les attributs
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => ['placeholder' => 'Confirmer mot de passe'], // Déplacement de 'attr' ici pour spécifier les attributs
                ],
            ]);
        // ->add('status', ChoiceType::class, [
        //     "required" => true,
        //     "expanded" => true,
        //     "multiple" => false,

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
        // ]);

    }



    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
