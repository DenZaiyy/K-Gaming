<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MultiAvatars extends AbstractController
{
  public function __construct(private readonly HttpClientInterface $client)
  {
  }

  public function api($imageID)
  {
    $response = $this->client->request(
      "GET",
      "https://api.multiavatar.com/" . $imageID . ".png"
    );

    return $response->getInfo("url");
  }

  public function getRandomUserAvatar($user): string
  {
    return $this->api($user . "-" . rand(1, 1000));
  }

  public function getRandomAvatar(): string
  {
    return rand(1, 1000);
  }

  public function getEightRandomlyAvatar(): array
  {
    for ($i = 0; $i < 8; $i++) {
      $avatars[] = $this->api($this->getRandomAvatar());
    }
    return $avatars;
  }

  public function setAvatarUrl($imageID)
  {
    return $this->api($imageID);
  }
}
