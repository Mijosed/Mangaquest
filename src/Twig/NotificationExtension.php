<?php

namespace App\Twig;

use App\Repository\NotificationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationExtension extends AbstractExtension
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private Security $security
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_notifications', [$this, 'getNotifications']),
            new TwigFunction('get_unread_notifications', [$this, 'getUnreadNotifications']),
        ];
    }

    public function getNotifications(): array
    {
        $user = $this->security->getUser();
        if (!$user) {
            return [];
        }

        return $this->notificationRepository->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC'],
            10
        );
    }

    public function getUnreadNotifications(): array
    {
        $user = $this->security->getUser();
        if (!$user) {
            return [];
        }

        return $this->notificationRepository->findBy(
            ['user' => $user, 'isRead' => false]
        );
    }
} 