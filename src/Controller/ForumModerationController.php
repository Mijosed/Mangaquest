<?php

namespace App\Controller;

use App\Entity\Report;
use App\Entity\Topic;
use App\Entity\Post;
use App\Form\ReportType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/forum/moderation')]
class ForumModerationController extends AbstractController
{
    #[Route('/report/topic/{id}', name: 'app_forum_report_topic')]
    #[IsGranted('ROLE_USER')]
    public function reportTopic(Topic $topic, Request $request, EntityManagerInterface $entityManager): Response
    {
        $report = new Report();
        $report->setTopic($topic);
        $report->setReporter($this->getUser());

        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($report);
            $entityManager->flush();

            $this->addFlash('success', 'Le sujet a été signalé aux modérateurs.');
            return $this->redirectToRoute('app_forum_show', ['id' => $topic->getId()]);
        }

        return $this->render('forum/moderation/report.html.twig', [
            'form' => $form,
            'topic' => $topic
        ]);
    }

    #[Route('/report/post/{id}', name: 'app_forum_report_post')]
    #[IsGranted('ROLE_USER')]
    public function reportPost(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $report = new Report();
        $report->setPost($post);
        $report->setReporter($this->getUser());

        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($report);
            $entityManager->flush();

            $this->addFlash('success', 'Le message a été signalé aux modérateurs.');
            return $this->redirectToRoute('app_forum_show', ['id' => $post->getTopic()->getId()]);
        }

        return $this->render('forum/moderation/report.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }

    #[Route('/reports', name: 'app_forum_reports')]
    #[IsGranted('ROLE_MODERATOR')]
    public function reports(EntityManagerInterface $entityManager): Response
    {
        $reports = $entityManager->getRepository(Report::class)->findBy(
            ['resolved' => false],
            ['createdAt' => 'DESC']
        );

        return $this->render('forum/moderation/reports.html.twig', [
            'reports' => $reports
        ]);
    }

    #[Route('/delete/topic/{id}', name: 'app_forum_delete_topic')]
    #[IsGranted('ROLE_MODERATOR')]
    public function deleteTopic(Topic $topic, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($topic);
        $entityManager->flush();

        $this->addFlash('success', 'Le sujet a été supprimé.');
        return $this->redirectToRoute('app_forum_index');
    }

    #[Route('/delete/post/{id}', name: 'app_forum_delete_post')]
    #[IsGranted('ROLE_MODERATOR')]
    public function deletePost(Post $post, EntityManagerInterface $entityManager): Response
    {
        $topicId = $post->getTopic()->getId();
        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', 'Le message a été supprimé.');
        return $this->redirectToRoute('app_forum_show', ['id' => $topicId]);
    }
} 