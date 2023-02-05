<?php

namespace App\Security;

use App\Entity\Hostel;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class HostelVoter extends Voter
{
    private const CREATE = 'created';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::CREATE])) {
            return false;
        }

        if (!$subject instanceof Hostel) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if (!$subject instanceof Hostel) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
                return $this->canCreate($subject, $user);
        }

        throw new LogicException('This code should not be reached!');
    }

    private function canCreate(Hostel $hostel, User $user): bool
    {
        return $user === $hostel->getOwner();
    }
}