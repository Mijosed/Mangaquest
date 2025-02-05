<?php

namespace App\Controller\Admin;

use App\Entity\Manga;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

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
            ->setSearchFields(['title', 'description', 'status', 'mangaDexId'])
            ->setDefaultSort(['title' => 'ASC'])
            ->setPaginatorPageSize(20)
            ->showEntityActionsInlined()
            ->setPageTitle('index', 'Gestion des Mangas')
            ->setPageTitle('new', 'Ajouter un manga')
            ->setPageTitle('edit', fn (Manga $manga) => sprintf('Modifier le manga <b>%s</b>', $manga->getTitle()))
            ->setPageTitle('detail', fn (Manga $manga) => $manga->getTitle());
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setIcon('fa fa-plus')->setLabel('Ajouter un manga');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fa fa-edit');
            })
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setIcon('fa fa-eye');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-trash');
            })
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('title', 'Titre'))
            ->add(TextFilter::new('mangaDexId', 'MangaDex ID'))
            ->add(ChoiceFilter::new('status', 'Statut')->setChoices([
                'En cours' => 'ongoing',
                'Terminé' => 'completed',
                'En pause' => 'hiatus',
                'Abandonné' => 'cancelled'
            ]))
            ->add(NumericFilter::new('year', 'Année'));
    }

    public function configureFields(string $pageName): iterable
    {
        $basicFields = [
            IdField::new('id')->hideOnForm(),
            TextField::new('mangaDexId', 'MangaDex ID')
                ->setHelp('Identifiant unique du manga sur MangaDex (laissez vide pour création manuelle)')
                ->setRequired(false),
            TextField::new('title', 'Titre')
                ->setColumns(12),
            TextEditorField::new('description', 'Description')
                ->hideOnIndex()
                ->setColumns(12)
                ->setHelp('Description détaillée du manga'),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'En cours' => 'ongoing',
                    'Terminé' => 'completed',
                    'En pause' => 'hiatus',
                    'Abandonné' => 'cancelled'
                ])
                ->renderAsBadges([
                    'ongoing' => 'success',
                    'completed' => 'primary',
                    'hiatus' => 'warning',
                    'cancelled' => 'danger'
                ]),
            IntegerField::new('year', 'Année')
                ->setHelp('Année de publication')
        ];

        $coverField = ImageField::new('coverImage', 'Couverture')
            ->setBasePath('uploads/manga/covers')
            ->setUploadDir('public/uploads/manga/covers')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setFormTypeOptions([
                'required' => false,
            ])
            ->setTemplatePath('admin/fields/manga_cover.html.twig')
            ->formatValue(function ($value, $entity) {
                if (!$value || !$entity instanceof Manga) {
                    return null;
                }
                if (str_starts_with($entity->getMangaDexId(), 'manual_')) {
                    return '/uploads/manga/covers/' . $value;
                }
                return sprintf('https://uploads.mangadex.org/covers/%s/%s',
                    $entity->getMangaDexId(),
                    $value
                );
            });

        if (Crud::PAGE_INDEX === $pageName) {
            $coverField->setColumns(2);
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            $coverField->setColumns(4);
        } else {
            $coverField->setColumns(12);
        }

        return array_merge($basicFields, [$coverField]);
    }

    public function createEntity(string $entityFqcn)
    {
        $manga = new Manga();
        $manga->setStatus('ongoing');
        $manga->setMangaDexId('manual_' . uniqid());
        return $manga;
    }
}