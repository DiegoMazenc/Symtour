<?php

namespace App\Security\Voter;

use App\Entity\BandMember;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

// ...

class BandVoter extends Voter
{
    public const BAND_MEMBER_EDIT = 'band_member_edit';
    public const BAND_MEMBER_VIEW = 'band_member_view';
    public const BAND_MEMBER = 'band_member';


    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::BAND_MEMBER_EDIT, self::BAND_MEMBER_VIEW, self::BAND_MEMBER])
            && $subject instanceof \App\Entity\BandMember;
    }

    protected function voteOnAttribute(string $attribute, $bandMember, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }


        switch ($attribute) {
            case self::BAND_MEMBER_EDIT:
                return $this->canEdit($bandMember, $user);
            case self::BAND_MEMBER_VIEW:
                return $this->canView($bandMember, $user);
            case self::BAND_MEMBER:
                return $this->isUserInBand($bandMember, $user);
        }

        return false;
    }

    private function isAdmin(BandMember $bandMember): bool
    {
        return $bandMember->getStatus() === 'admin';
    }

    private function canEdit(BandMember $bandMember, User $user): bool
    {
        return $this->isUserInBand($bandMember, $user) && $this->isAdmin($bandMember);
    }

    private function canView(BandMember $bandMember, User $user): bool
    {
        return $this->isUserInBand($bandMember, $user) && $bandMember->getStatus() === "member";
    }

    private function isUserInBand(BandMember $bandMember, User $user): bool
    {
          return $bandMember->getProfil() !== null &&  $bandMember->getProfil()->getIdUser()->getId() ===  $user->getId();
    }
}
