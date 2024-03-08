<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
  /**
   * TODO: Vérifier l'utilité de cette classe et trouver une bonne raison de l'utilisé
   */

  protected function supports(string $attribute, mixed $subject): bool
  {
    return true;
  }

  protected function voteOnAttribute(
    string $attribute,
    mixed $subject,
    TokenInterface $token
  ): bool {
    $user = $token->getUser();

    if (!$user instanceof User) {
      return false;
    } elseif ($user->isIsBanned()) {
      session_unset();
      return false;
    } elseif ($user->getRoles() == ["ROLE_USER"]) {
      return false;
    }
    return true;
  }
}
