<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Bundle\SecurityBundle\Security;

class RedirectBannedSubscriber implements EventSubscriberInterface
{
    private Security $security;
    private RouterInterface $router;

    public function __construct(Security $security, RouterInterface $router)
    {
        $this->security = $security;
        $this->router = $router;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        // Vérifie si l'utilisateur est authentifié
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        // Vérifie si l'utilisateur a le rôle ROLE_BANNED
        if ($this->security->isGranted('ROLE_BANNED')) {
            $route = $event->getRequest()->attributes->get('_route');

            // Autorise uniquement l'accès à la page bannie
            if ($route !== 'banned_page') {
                $event->setController(function () {
                    return new RedirectResponse($this->router->generate('banned_page'));
                });
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
