<?php

namespace App\Form;

use App\Entity\Topic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class TopicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Entrez le titre de votre sujet'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => [
                    'placeholder' => 'Décrivez votre sujet...',
                    'rows' => 10
                ]
            ])
            ->add('isNsfw', CheckboxType::class, [
                'label' => 'Contenu sensible (NSFW)',
                'required' => false,
                'help' => 'Cochez cette case si votre contenu est réservé aux adultes'
            ])
            ->add('hasSpoiler', CheckboxType::class, [
                'label' => 'Contient des spoilers',
                'required' => false,
            ])
            ->add('spoilerWarning', TextType::class, [
                'label' => 'Avertissement spoiler',
                'required' => false,
                'help' => 'Ex: "Spoiler sur la saison 2 de One Piece"',
                'attr' => [
                    'placeholder' => 'Précisez le contenu du spoiler'
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (optionnelle)',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG ou WEBP)'
                    ])
                ],
                'help' => 'Format recommandé : 1200x400px, 2Mo maximum'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Topic::class,
        ]);
    }
} 