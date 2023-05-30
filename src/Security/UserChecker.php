<?php

namespace App\Security;

use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
	public function checkPreAuth(UserInterface $user)
	{
		if (!$user instanceof AppUser) {
			return;
		} elseif ($user->isIsBanned()) {
			session_unset();
		}
	}

	public function checkPostAuth(UserInterface $user)
	{
		if (!$user instanceof AppUser) {
			return;
		} elseif ($user->isIsBanned()) {
			session_unset();
		}
	}
}