<?php

namespace App\Form;

use App\Entity\MangaTopic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MangaTopicType extends TopicType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('mangaTitle', TextType::class, [
                'label' => 'Titre du manga',
                'attr' => [
                    'placeholder' => 'Ex: One Piece'
                ]
            ])
            ->add('chapter', TextType::class, [
                'label' => 'Chapitre',
                'attr' => [
                    'placeholder' => 'Ex: 1067'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MangaTopic::class,
        ]);
    }
} 