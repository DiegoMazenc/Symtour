<?php

namespace App\Security\Voter;

use App\Entity\HallMember;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class HallVoter extends Voter
{
    public const HALL_MEMBER_EDIT = 'hall_member_edit';
    public const HALL_MEMBER_VIEW = 'hall_member_view';
    public const HALL_MEMBER = 'hall_member';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::HALL_MEMBER_EDIT, self::HALL_MEMBER_VIEW, self::HALL_MEMBER])
            && $subject instanceof \App\Entity\HallMember;
    }

    protected function voteOnAttribute(string $attribute, $hallMember, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::HALL_MEMBER_EDIT:
                return $this->canEdit($hallMember, $user);
            case self::HALL_MEMBER_VIEW:
                return $this->canView($hallMember, $user);
            case self::HALL_MEMBER:
                return $this->isUserInBand($hallMember, $user);
        }    

        return false;
    }

    private function isAdmin(HallMember $hallMember): bool
    {
        return $hallMember->getStatus() === 'admin';
    }

    private function canEdit(HallMember $hallMember, User $user): bool
    {
        return $this->isUserInBand($hallMember, $user) && $this->isAdmin($hallMember);
    }

    private function canView(HallMember $hallMember, User $user): bool
    {
        return $this->isUserInBand($hallMember, $user) && $hallMember->getStatus() === "member";
    }

    private function isUserInBand(HallMember $hallMember, User $user): bool
    {
          return $hallMember->getProfile() !== null &&  $hallMember->getProfile()->getIdUser()->getId() ===  $user->getId();
    }
}
