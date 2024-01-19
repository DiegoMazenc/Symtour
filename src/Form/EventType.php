<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\band;
use App\Entity\hall;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    // 'Validé' => 1,
                    // 'Refusé' => 2,
                    'En Cours' => 3,
                ],
                'data' => 3, 
                'label' => false,
                'attr' => [
                    'style' => 'display: none;', 
                ],

            ])
            ->add('hall', EntityType::class, [
                'class' => Hall::class,
                'choice_label' => 'name',
            ])
            ->add('band', EntityType::class, [
                'class' => Band::class,
                'choice_label' => 'name',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
