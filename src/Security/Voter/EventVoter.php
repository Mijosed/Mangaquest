<?php

namespace App\Security\Voter;

use App\Entity\Event;
use App\Entity\User;
use App\Entity\Organizer;
use App\Entity\Participant;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class EventVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const PARTICIPATE = 'participate';
    const CREATE = 'create';

    public function __construct(
        private Security $security
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === self::CREATE) {
            return true;
        }

        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE, self::PARTICIPATE])
            && ($subject instanceof Event || $subject === null);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return $attribute === self::VIEW;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return match($attribute) {
            self::VIEW => true,
            self::CREATE => $this->canCreate($user),
            self::EDIT => $this->canEdit($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            self::PARTICIPATE => $this->canParticipate($subject, $user),
            default => false,
        };
    }

    private function canCreate(UserInterface $user): bool
    {
        // Any authenticated user can create an event
        return $user instanceof User;
    }

    private function canEdit(Event $event, UserInterface $user): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $event->getOrganizers()->contains($user) || $user === $event->getCreator();
    }

    private function canDelete(Event $event, UserInterface $user): bool
    {
        return $this->canEdit($event, $user);
    }

    private function canParticipate(Event $event, UserInterface $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        // Cannot participate if creator or organizer
        if ($event->getCreator() === $user || $event->getOrganizers()->contains($user)) {
            return false;
        }

        // Allow all users to participate (they all have ROLE_PARTICIPANT)
        return true;
    }
}
