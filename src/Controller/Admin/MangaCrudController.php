<?php

namespace App\Controller\Admin;

use App\Entity\Manga;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class MangaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Manga::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Manga')
            ->setEntityLabelInPlural('Mangas')
            ->setSearchFields(['title', 'description', 'status'])
            ->setPaginatorPageSize(15)
            ->setDefaultSort(['title' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('mangaDexId', 'MangaDex ID'),
            TextField::new('title', 'Titre'),
            TextEditorField::new('description')->hideOnIndex(),
            TextField::new('status', 'Statut'),
            IntegerField::new('year', 'Année'),
            TextField::new('coverImage', 'Image de couverture')->hideOnIndex(),
        ];
    }
} 