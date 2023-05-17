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
        $response = $this->client->request(
            'POST',
            'https://api.igdb.com/v4/games/',
            [
                'headers' => [
                    'Client-ID' => $this->getParameter('app.client_id'),
                    'Authorization' => $this->getParameter('app.authorization')
                ],
                'body' => $data
            ]

        );

        return $response->toArray();
    }

    // function to get cover of a game and using the function connectAPI to connect to the api
	public function getCoverByGame($game) : array
	{
        return $this->connectAPI(
            'fields id, name, cover.image_id; where name = "' . $game . '";'
        );
	}

    // function to get the summary of a game and using the function connectAPI to connect to the api
    public function getSummaryByGame($game) : array
    {
        return $this->connectAPI(
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
