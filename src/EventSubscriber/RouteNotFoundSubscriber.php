<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RouteNotFoundSubscriber implements EventSubscriberInterface
{
  private $urlGenerator;

  public function __construct(UrlGeneratorInterface $urlGenerator)
  {
    $this->urlGenerator = $urlGenerator;
  }

  public function onKernelException(ExceptionEvent $event): void
  {
    $exception = $event->getThrowable();

    if ($exception instanceof NotFoundHttpException) {
      //Generate the URL for the desired redirect destination
      $redirectUrl = $this->urlGenerator->generate("app_404");

      // Create a new RedirectResponse object with the desired URL
      $response = new RedirectResponse($redirectUrl);

      // Set the new response object as the response to be sent to the browser
      $event->setResponse($response);
    }
  }

  public static function getSubscribedEvents(): array
  {
    return [
      KernelEvents::EXCEPTION => "onKernelException",
    ];
  }
}
