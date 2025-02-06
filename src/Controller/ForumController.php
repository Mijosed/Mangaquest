<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Entity\Post;
use App\Entity\Report;
use App\Form\TopicType;
use App\Form\PostType;
use App\Form\ReportType;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\NotificationService;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Entity\MangaTopic;
use App\Form\MangaTopicType;
use App\Entity\AnimeTopic;
use App\Form\AnimeTopicType;

#[Route('/forum')]
class ForumController extends AbstractController
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    #[Route('/', name: 'app_forum_index', methods: ['GET'])]
    public function index(Request $request, TopicRepository $topicRepository): Response
    {
        $filters = [
            'type' => $request->query->get('type'),
            'date' => $request->query->get('date'),
            'sort' => $request->query->get('sort', 'createdAt'),
            'order' => $request->query->get('order', 'DESC'),
        ];

        // Gestion du NSFW
        if (!$this->getUser() || !$this->getUser()->isAdult()) {
            $filters['nsfw'] = false;
        }

        $page = $request->query->getInt('page', 1);
        $result = $topicRepository->findFilteredTopics($filters, $page);

        return $this->render('forum/index.html.twig', [
            'topics' => $result['topics'],
            'currentPage' => $result['currentPage'],
            'lastPage' => $result['lastPage'],
            'totalTopics' => $result['totalItems'],
            'filters' => $filters,
            'popular_topics' => $topicRepository->findBy(['isApproved' => true], ['views' => 'DESC'], 5)
        ]);
    }

    #[Route('/new/manga', name: 'app_forum_new_manga')]
    #[IsGranted('ROLE_USER')]
    public function newMangaTopic(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $topic = new MangaTopic();
        $form = $this->createForm(MangaTopicType::class, $topic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('topics_directory'),
                        $newFilename
                    );
                    $topic->setImageFilename($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de l\'image');
                }
            }

            $topic->setAuthor($this->getUser());
            $topic->setIsApproved(false);
            $entityManager->persist($topic);
            $entityManager->flush();

            $this->notificationService->notifyAdminsNewTopic($topic);
            $this->addFlash('info', 'Votre sujet manga a été soumis et sera visible après validation par un administrateur.');
            return $this->redirectToRoute('app_forum_index');
        }

        return $this->render('forum/new.html.twig', [
            'form' => $form,
            'type' => 'manga'
        ]);
    }

    #[Route('/new/anime', name: 'app_forum_new_anime')]
    #[IsGranted('ROLE_USER')]
    public function newAnimeTopic(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $topic = new AnimeTopic();
        $form = $this->createForm(AnimeTopicType::class, $topic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('topics_directory'),
                        $newFilename
                    );
                    $topic->setImageFilename($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de l\'image');
                }
            }

            $topic->setAuthor($this->getUser());
            $topic->setIsApproved(false);
            $entityManager->persist($topic);
            $entityManager->flush();

            $this->notificationService->notifyAdminsNewTopic($topic);
            $this->addFlash('info', 'Votre sujet anime a été soumis et sera visible après validation par un administrateur.');
            return $this->redirectToRoute('app_forum_index');
        }

        return $this->render('forum/new.html.twig', [
            'form' => $form,
            'type' => 'anime'
        ]);
    }

    #[Route('/new/topic', name: 'app_forum_new_topic')]
    #[IsGranted('ROLE_USER')]
    public function newTopic(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $topic = new Topic();
        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('topics_directory'),
                        $newFilename
                    );
                    $topic->setImageFilename($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de l\'image');
                }
            }

            $topic->setAuthor($this->getUser());
            $topic->setIsApproved(false);
            $entityManager->persist($topic);
            $entityManager->flush();

            $this->notificationService->notifyAdminsNewTopic($topic);
            $this->addFlash('info', 'Votre sujet a été soumis et sera visible après validation par un administrateur.');
            return $this->redirectToRoute('app_forum_index');
        }

        return $this->render('forum/new.html.twig', [
            'form' => $form,
            'type' => 'general'
        ]);
    }

    #[Route('/{id}', name: 'app_forum_show', methods: ['GET', 'POST'])]
    public function show(Topic $topic, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$topic->isApproved() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Ce sujet est en attente de validation.');
        }

        // Vérification NSFW
        if ($topic->isNsfw()) {
            if (!$this->getUser()) {
                throw $this->createAccessDeniedException('Vous devez être connecté pour voir ce contenu.');
            }
        }

        // Incrémenter le compteur de vues
        $topic->setViews($topic->getViews() + 1);
        $entityManager->flush();

        // Formulaire pour ajouter une réponse
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->getUser()) {
                // Utiliser 'danger' au lieu de 'error'
                $this->addFlash('danger', 'Vous devez être connecté pour répondre.');
                return $this->redirectToRoute('app_login');
            }

            $post->setAuthor($this->getUser());
            $post->setTopic($topic);
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_forum_show', ['id' => $topic->getId()]);
        }

        return $this->render('forum/show.html.twig', [
            'topic' => $topic,
            'form' => $form
        ]);
    }

    #[Route('/post/{id}/edit', name: 'app_post_edit')]
    public function editPost(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur peut modifier ce post
        $this->denyAccessUnlessGranted('POST_EDIT', $post);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Votre réponse a été modifiée avec succès.');
            return $this->redirectToRoute('app_forum_show', ['id' => $post->getTopic()->getId()]);
        }

        return $this->render('forum/edit_post.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }

    #[Route('/post/{id}/delete', name: 'app_post_delete', methods: ['POST'])]
    public function deletePost(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('POST_DELETE', $post);

        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $topicId = $post->getTopic()->getId();
            $entityManager->remove($post);
            $entityManager->flush();

            $this->addFlash('success', 'Votre réponse a été supprimée.');
            return $this->redirectToRoute('app_forum_show', ['id' => $topicId]);
        }

        throw new AccessDeniedException('Token CSRF invalide.');
    }
}
