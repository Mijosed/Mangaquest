<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_user_profile')]
    public function profile(): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        if (!$user) {
            // Si aucun utilisateur connecté, redirigez vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        // Affiche la page de profil
        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);

    }
}
