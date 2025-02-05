<?php

namespace App\Controller\Admin;

use App\Entity\Topic;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\NotificationService;

class TopicCrudController extends AbstractCrudController
{
    public function __construct(
        private NotificationService $notificationService
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Topic::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Topic')
            ->setEntityLabelInPlural('Topics')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(20);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Titre'),
            TextEditorField::new('content', 'Contenu'),
            AssociationField::new('author', 'Auteur')
                ->setFormTypeOption('choice_label', 'email')
                ->onlyOnIndex(),
            ImageField::new('imageFilename', 'Image')
                ->setBasePath('uploads/topics')
                ->setUploadDir('public/uploads/topics')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            BooleanField::new('isApproved', 'Approuvé'),
            BooleanField::new('isNsfw', 'NSFW'),
            BooleanField::new('hasSpoiler', 'Spoiler'),
            TextField::new('spoilerWarning', 'Avertissement spoiler'),
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Créer un topic');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setLabel('Modifier');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setLabel('Supprimer');
            })
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setLabel('Voir');
            });
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $originalTopic = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);
        $wasApproved = $originalTopic['isApproved'] ?? false;
        
        parent::updateEntity($entityManager, $entityInstance);
        
        if (!$wasApproved && $entityInstance->isApproved()) {
            $this->notificationService->notifyAuthorTopicApproved($entityInstance);
        }
    }
}
