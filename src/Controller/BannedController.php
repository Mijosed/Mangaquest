<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BannedController extends AbstractController
{
    #[Route('/banned', name: 'banned_page')]
    public function banned(): Response
    {
        return $this->render('banned.html.twig');
    }
}
