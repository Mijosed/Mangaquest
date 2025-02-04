<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;

#[Route('/admin/topics')]
#[IsGranted('ROLE_ADMIN')]
class TopicModerationController extends AbstractController
{
    #[Route('/pending', name: 'app_admin_topics_pending')]
    public function pendingTopics(TopicRepository $topicRepository): Response
    {
        return $this->render('admin/topics/pending.html.twig', [
            'topics' => $topicRepository->findBy(['isApproved' => false], ['createdAt' => 'DESC'])
        ]);
    }

    #[Route('/{id}/approve', name: 'app_admin_topic_approve', methods: ['POST'])]
    public function approveTopic(Topic $topic, Request $request, EntityManagerInterface $entityManager): Response
    {
        $topic->setIsApproved(true);
        $topic->setHasSpoiler($request->request->getBoolean('hasSpoiler'));
        $topic->setSpoilerWarning($request->request->get('spoilerWarning'));
        
        $entityManager->flush();

        $this->addFlash('success', 'Le sujet a été approuvé.');
        return $this->redirectToRoute('app_admin_topics_pending');
    }

    #[Route('/{id}/reject', name: 'app_admin_topic_reject')]
    public function rejectTopic(Topic $topic, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($topic);
        $entityManager->flush();

        $this->addFlash('success', 'Le sujet a été rejeté et supprimé.');
        return $this->redirectToRoute('app_admin_topics_pending');
    }
} 