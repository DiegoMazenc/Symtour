<?php

namespace App\Security\Voter;

use App\Entity\Profil;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const USER_EDIT = 'user_edit';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::USER_EDIT])
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, $userConnect, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if(null === $userConnect->getId()) return false;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::USER_EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canEdit($userConnect, $user);
                break;
        }

        return false;
    }

    private function canEdit(User $userConnect, User $user){
        return $user === $userConnect;
    }

}
