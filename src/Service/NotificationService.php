<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\Topic;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function notifyAdminsNewTopic(Topic $topic): void
    {
        // Trouver tous les admins
        $admins = $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_ADMIN%')
            ->getQuery()
            ->getResult();
        
        foreach ($admins as $admin) {
            $notification = new Notification();
            $notification->setUser($admin);
            $notification->setMessage(sprintf(
                'Nouveau topic à modérer : "%s" par %s',
                $topic->getTitle(),
                $topic->getAuthor()->getEmail()
            ));
            $notification->setLink($this->urlGenerator->generate('admin', [
                'crudController' => 'App\Controller\Admin\TopicCrudController',
                'crudAction' => 'detail',
                'entityId' => $topic->getId()
            ]));
            
            $this->entityManager->persist($notification);
        }
        
        $this->entityManager->flush();
    }

    public function notifyAuthorTopicApproved(Topic $topic): void
    {
        $notification = new Notification();
        $notification->setUser($topic->getAuthor());
        $notification->setMessage(sprintf(
            'Votre topic "%s" a été approuvé',
            $topic->getTitle()
        ));
        $notification->setLink($this->urlGenerator->generate('app_forum_show', ['id' => $topic->getId()]));
        
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }
} 