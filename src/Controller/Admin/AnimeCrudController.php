<?php

namespace App\Controller\Admin;

use App\Entity\Anime;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class AnimeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Anime::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Anime')
            ->setEntityLabelInPlural('Animes')
            ->setSearchFields(['title', 'description', 'studio', 'genre', 'posterImage'])
            ->setPaginatorPageSize(15)
            ->setDefaultSort(['title' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter un anime');
            });
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Titre'),
            TextEditorField::new('description', 'Description')
                ->hideOnIndex(),
            TextField::new('studio', 'Studio d\'animation'),
            IntegerField::new('episodes', 'Nombre d\'épisodes'),
            TextField::new('genre', 'Genres'),
            TextField::new('releaseDate', 'Date de sortie (YYYY-MM-DD)'),
        ];

        // Afficher l'image de l'anime
        if ($pageName === Crud::PAGE_INDEX || $pageName === Crud::PAGE_DETAIL) {
            $fields[] = ImageField::new('posterImage', 'Poster')
                ->setBasePath('uploads/animes')
                ->onlyOnIndex();
        } else {
            $fields[] = TextField::new('posterImage', 'URL du poster');
        }

        return $fields;
    }
} 