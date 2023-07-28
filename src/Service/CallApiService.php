<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CallApiService extends AbstractController
{
	public function __construct(private readonly HttpClientInterface $client)
	{
	}

    // Function to create connection with api (IGDB)
    public function connectAPI($data) : array
    {
        // Effectue une requête HTTP avec la méthode POST sur l'URL de l'API IGDB
        $response = $this->client->request(
            // Méthode HTTP utilisée
            'POST',
            // URL de l'API IGDB
            'https://api.igdb.com/v4/games/',
            // Options de la requête
            [
                // En-têtes de la requête (obligatoire)
                'headers' => [
                    // Client-ID et Authorization sont des en-têtes obligatoires pour se connecter à l'API IGDB
                    'Client-ID' => $this->getParameter('app.client_id'),
                    'Authorization' => $this->getParameter('app.authorization')
                ],
                // Corps de la requête (obligatoire) avec la requête à effectuer grâce à la variable $data
                'body' => $data
            ]

        );

        return $response->toArray();
    }

    // function to get cover of a game and using the function connectAPI to connect to the api
	public function getCoverByGame($game) : array
	{
        // Utilisation de la fonction connectAPI pour se connecter à l'API IGDB
        return $this->connectAPI(
            // Requête à effectuer pour récupérer la cover du jeu remplaçant le nom du jeu par la variable $game
            'fields name, cover.image_id; where name = "' . $game . '";'
        );
	}

    // function to get the summary of a game and using the function connectAPI to connect to the api
    public function getSummaryByGame($game) : array
    {
        // Utilisation de la fonction connectAPI pour se connecter à l'API IGDB
        return $this->connectAPI(
            // Requête à effectuer pour récupérer le résumé du jeu remplaçant le nom du jeu par la variable $game
            'fields name, summary; where name = "' . $game . '";'
        );
    }

    // function to get screenshots of a game and using the function connectAPI to connect to the api
    public function getScreenshotByGame($game) : array
    {
        return $this->connectAPI(
            'fields name, screenshots.image_id; where name = "' . $game . '";'
        );
    }

    // function to get videos of a game and using the function connectAPI to connect to the api
    public function getVideosByGame($game) : array
    {
        return $this->connectAPI(
            'fields name, videos.name, videos.video_id; where name = "' . $game . '";'
        );
    }
}
