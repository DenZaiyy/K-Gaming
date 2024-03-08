<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
  public function onKernelRequest(RequestEvent $event): void
  {
    $request = $event->getRequest();
    if (!$request->hasPreviousSession()) {
      return;
    }

    if ($request->cookies->get("_locale")) {
      $request->setLocale($request->cookies->get("_locale"));
    } else {
      $request->cookies->set("_locale", $request->getDefaultLocale());
      $request->setLocale($request->getDefaultLocale());
    }
  }

  public static function getSubscribedEvents(): array
  {
    return [
      // must be registered before (i.e. with a higher priority than) the default Locale listener
      KernelEvents::REQUEST => [["onKernelRequest", 20]],
    ];
  }
}
