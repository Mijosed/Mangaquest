<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    #[Route('/notification/{id}/read', name: 'app_notification_read')]
    public function markAsRead(
        Notification $notification,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier que l'utilisateur est le propriétaire de la notification
        if ($notification->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $notification->setIsRead(true);
        $entityManager->flush();

        return $this->redirect($notification->getLink());
    }
} 