<?php

namespace App\Security\Voter;

use App\Entity\Profil;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfilVoter extends Voter
{
    public const PROFIL_EDIT = 'profil_edit';
    public const PROFIL_VIEW = 'profil_view';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::PROFIL_EDIT , self::PROFIL_VIEW])
            && $subject instanceof \App\Entity\Profil;
    }

    protected function voteOnAttribute(string $attribute, $profil, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if(null === $profil->getIdUser()) return false;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::PROFIL_EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canEdit($profil, $user);
                break;
            case self::PROFIL_VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }

    private function canEdit(Profil $profil, User $user){
        return $user === $profil->getIdUser();
    }

    private function canView(Profil $profil, User $user){

    }
}
