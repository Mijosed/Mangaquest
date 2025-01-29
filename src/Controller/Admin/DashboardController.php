<?php

namespace App\Controller\Admin;

use App\Entity\Manga;
use App\Entity\Anime;
use App\Entity\User;
use App\Repository\MangaRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private MangaRepository $mangaRepository,
        private UserRepository $userRepository
    ) {}

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Récupération des statistiques
        $totalMangas = $this->mangaRepository->count([]);
        $totalUsers = $this->userRepository->count([]);
        $mangasByStatus = $this->mangaRepository->countByStatus();
        $recentMangas = $this->mangaRepository->findBy([], ['id' => 'DESC'], 5);

        return $this->render('admin/dashboard.html.twig', [
            'total_mangas' => $totalMangas,
            'total_users' => $totalUsers,
            'mangas_by_status' => $mangasByStatus,
            'recent_mangas' => $recentMangas,
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
        
        yield MenuItem::section('Navigation');
        yield MenuItem::linkToRoute('Retour au site', 'fas fa-arrow-left', 'app_manga_list');
    }
} 