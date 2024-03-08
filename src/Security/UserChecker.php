<?php

namespace App\Security;

use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
  /**
   * TODO: Vérifier l'utilité de cette classe et trouver une bonne raison de l'utilisé
   */
  public function checkPreAuth(UserInterface $user): void
  {
    if (!$user instanceof AppUser) {
      return;
    } elseif ($user->isIsBanned()) {
      session_unset();
    }
  }

  public function checkPostAuth(UserInterface $user): void
  {
    if (!$user instanceof AppUser) {
      return;
    } elseif ($user->isIsBanned()) {
      session_unset();
    }
  }
}
