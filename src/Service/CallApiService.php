<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CallApiService extends AbstractController
{
	public function __construct(private HttpClientInterface $client)
	{

	}

	public function getInfosGame($game): array
	{
		$response = $this->client->request(
			'POST',
			'https://api.igdb.com/v4/games/',
			[
				'headers' => [
					'Client-ID' => $this->getParameter('app.client_id'),
					'Authorization' => $this->getParameter('app.authorization')
				],
				'body' => 'fields id, name, cover.image_id, summary; where name = "' . $game . '";'
			]

		);

		return $response->toArray();
	}
}
