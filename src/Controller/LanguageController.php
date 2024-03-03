<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LanguageController extends AbstractController
{
	#[Route('/change-language/{_locale}', name: 'change_language')]
	public function index (Request $request): Response
	{
		$locale = $request->attributes->get('_locale');
		$referer = $request->headers->get('referer');

		// Check if the referer URL contains a locale (e.g., 'en' or 'fr')
		if (preg_match('/\/(en|fr)\//', $referer, $matches)) {
			$newReferer = preg_replace('/\/(en|fr)\//', '/' . $locale . '/', $referer, 1);
			$redirect = $this->redirect($newReferer);
			$redirect->headers->setCookie(new Cookie('_locale', $locale));
			return $redirect;
		}

		$response = $this->redirectToRoute('app_home', ['_locale' => $locale]);
		$response->headers->setCookie(new Cookie('_locale', $locale));
		return $response;
	}
}
