<?php

namespace App\Form;

use App\Entity\AnimeTopic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnimeTopicType extends TopicType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('animeTitle', TextType::class, [
                'label' => 'Titre de l\'anime',
                'attr' => [
                    'placeholder' => 'Ex: One Piece'
                ]
            ])
            ->add('episode', TextType::class, [
                'label' => 'Épisode',
                'attr' => [
                    'placeholder' => 'Ex: 1067'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AnimeTopic::class,
        ]);
    }
} 