<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Organizer;

#[Route('/event')]
class EventController extends AbstractController
{
    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $events = $entityManager->getRepository(Event::class)->findAll();
        return $this->render('event/index.html.twig', ['events' => $events]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('create', null);

        $event = new Event();
        $user = $this->getUser();
        $event->setCreator($user);
        
        // Only add as organizer if user is an Organizer
        if ($user instanceof Organizer) {
            $event->addOrganizer($user);
        } else {
            // Generate a unique identifier
            $uniqueId = uniqid();
            $organizerEmail = sprintf('organizer.%s.%s', $uniqueId, $user->getEmail());
            
            // Create new Organizer with guaranteed unique email
            $organizer = new Organizer();
            $organizer->setEmail($organizerEmail);
            $organizer->setFirstName($user->getFirstName());
            $organizer->setLastName($user->getLastName());
            $organizer->setPassword($user->getPassword());
            $organizer->setOrganization(sprintf('Personal Organization - %s (%s)', $user->getFirstName(), $uniqueId));
            $organizer->setRoles(['ROLE_ORGANIZER']);
            $organizer->setVerified(true); // Changed from setIsVerified to setVerified
            
            $entityManager->persist($organizer);
            $event->addOrganizer($organizer);
        }
        
        $form = $this->createForm(EventType::class, $event);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();
            
            $this->addFlash('success', 'Event created successfully!');
            return $this->redirectToRoute('app_event_index');
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('edit', $event);
        
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('delete', $event);

        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_index');
    }

    #[Route('/{id}/participate', name: 'app_event_participate', methods: ['POST'])]
    public function participate(Event $event, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('participate', $event);

        $user = $this->getUser();
        
        if ($event->hasParticipant($user)) {
            $event->removeParticipant($user);
            $message = 'Participation cancelled';
        } else {
            $event->addParticipant($user);
            $message = 'Successfully registered for the event';
        }
        
        $entityManager->flush();
        
        $this->addFlash('success', $message);
        return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
    }
}
