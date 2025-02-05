<?php

namespace App\Controller;

use App\Repository\AnimeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Review;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Repository\FavoriteRepository;

#[Route('/anime')]
class AnimeController extends AbstractController
{
    #[Route('/', name: 'app_anime_index')]
    public function index(Request $request, AnimeRepository $animeRepository): Response
    {
        $search = $request->query->get('search');
        $letter = $request->query->get('letter');
        $sort = $request->query->get('sort', 'title');
        $order = $request->query->get('order', 'ASC');
        $page = $request->query->getInt('page', 1);
        $limit = 15; // Nombre d'animes par page

        $animes = $animeRepository->findBySearch($search, $letter, $sort, $order, $page, $limit);
        $total = $animeRepository->countBySearch($search, $letter);
        
        $maxPages = ceil($total / $limit);

        // Générer l'alphabet pour le filtre
        $alphabet = range('A', 'Z');

        return $this->render('anime/index.html.twig', [
            'animes' => $animes,
            'currentPage' => $page,
            'maxPages' => $maxPages,
            'total' => $total,
            'search' => $search,
            'letter' => $letter,
            'sort' => $sort,
            'order' => $order,
            'alphabet' => $alphabet
        ]);
    }

    #[Route('/{id}', name: 'app_anime_show')]
    public function show(
        int $id, 
        Request $request, 
        EntityManagerInterface $entityManager, 
        AnimeRepository $animeRepository,
        MailerInterface $mailer,
        FavoriteRepository $favoriteRepository
    ): Response {
        $anime = $animeRepository->find($id);

        if (!$anime) {
            throw $this->createNotFoundException('Anime non trouvé');
        }

        // Récupérer les reviews existantes
        $reviews = $entityManager->getRepository(Review::class)->findBy(
            ['anime' => $anime],
            ['createdAt' => 'DESC']
        );

        // Créer le formulaire de review si l'utilisateur est connecté
        $review = new Review();
        $form = null;
        
        if ($this->getUser()) {
            $form = $this->createForm(ReviewType::class, $review);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $review->setUser($this->getUser());
                $review->setAnime($anime);
                
                $entityManager->persist($review);
                $entityManager->flush();

                // Envoyer l'email de notification
                $email = (new Email())
                    ->from('noreply@mangaquest.com')
                    ->to('admin@mangaquest.com')
                    ->subject('Nouvelle review anime')
                    ->html($this->renderView(
                        'emails/new_review.html.twig',
                        [
                            'review' => $review,
                            'type' => 'anime',
                            'title' => $anime->getTitle()
                        ]
                    ));

                $mailer->send($email);

                $this->addFlash('success', 'Votre avis a été publié avec succès !');
                return $this->redirectToRoute('app_anime_show', ['id' => $id]);
            }
        }

        // Vérifier si l'anime est en favori pour l'utilisateur connecté
        $favorite = null;
        if ($this->getUser()) {
            $favorite = $favoriteRepository->findOneByUserAndAnime($this->getUser(), $anime);
        }

        return $this->render('anime/show.html.twig', [
            'anime' => $anime,
            'reviews' => $reviews,
            'form' => $form?->createView(),
            'favorite' => $favorite,
        ]);
    }
}
