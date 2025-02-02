<?php

namespace App\Controller\Admin;

use App\Entity\Manga;
use App\Entity\Anime;
use App\Entity\User;
use App\Entity\Contact;
use App\Repository\MangaRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $mangaCount = $this->entityManager->getRepository(Manga::class)->count([]);
        $animeCount = $this->entityManager->getRepository(Anime::class)->count([]);

        return $this->render('admin/dashboard.html.twig', [
            'manga_count' => $mangaCount,
            'anime_count' => $animeCount,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Administration')
            ->setFaviconPath('favicon.ico');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        
        yield MenuItem::section('Contenu');
        yield MenuItem::linkToCrud('Mangas', 'fas fa-book', Manga::class);
        yield MenuItem::linkToCrud('Animes', 'fas fa-tv', Anime::class);
        
        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class)
            ->setPermission('ROLE_ADMIN');
        
        yield MenuItem::section('Messages');
        yield MenuItem::linkToCrud('Messages de contact', 'fas fa-envelope', Contact::class);
        
        yield MenuItem::section('Navigation');
        yield MenuItem::linkToRoute('Retour au site', 'fas fa-arrow-left', 'app_manga_list');
    }
} 