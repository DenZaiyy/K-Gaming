<?php

namespace App\Service;

use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\CachingHttpClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallApiService extends AbstractController
{
    public function __construct(private HttpClientInterface $client, private $targetStore, private readonly EntityManagerInterface $em)
    {
        $store = new Store($this->targetStore);
        $this->client = HttpClient::create();
        $this->client = new CachingHttpClient($this->client, $store);
    }

    public function getInfosByGames(array $options): array
    {
        $games = [];
        if (!empty($options)) {

            $gamesDB = $this->em->getRepository(Game::class)->findGamesByOptions($options);

            $gamesSlug = [];
            foreach ($gamesDB as $game) {
                $gamesSlug[] = $game->getSlug();
            }

            $slugs = implode('","', $gamesSlug);

            $gamesInfos = $this->connectAPI(
                'fields name, slug, summary, cover.image_id, screenshots.image_id; where slug = ("' . $slugs . '"); limit 50;'
            );

            foreach ($gamesDB as $game) {
                $gameName = $game->getLabel();

                foreach ($gamesInfos as $info) {
                    if ($info['name'] === $gameName) {
                        $games[] = ["game" => $game, "infos" => $info];
                    }
                }
            }

            return $games;
        }
        return $games;
    }

    // Function to create connection with api (IGDB) and get informations about games
    public function connectAPI($data): array
    {
        // Effectue une requête HTTP avec la méthode POST sur l'URL de l'API IGDB
        $response = $this->client->request(// Méthode HTTP utilisée
            "POST", // URL de l'API IGDB
            "https://api.igdb.com/v4/games/", // Options de la requête
            [
                // En-têtes de la requête (obligatoire)
                "headers" => [
                    // Client-ID et Authorization sont des en-têtes obligatoires pour se connecter à l'API IGDB
                    "Client-ID" => $this->getParameter("app.client_id"),
                    "Authorization" => $this->getParameter("app.authorization")
                ],
                // Corps de la requête (obligatoire) avec la requête à effectuer grâce à la variable $data
                "body" => $data
            ]
        );

        return $response->toArray();
    }

    // function to get cover of a game and using the function connectAPI to connect to the api
    public function getCoverByGame($game): array
    {
        // Utilisation de la fonction connectAPI pour se connecter à l'API IGDB
        return $this->connectAPI('fields name, cover.image_id; where name = "' . $game . '";');
    }
}
