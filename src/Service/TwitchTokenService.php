<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Dotenv\Dotenv;

class TwitchTokenService extends AbstractController
{
    private mixed $clientId;
    private mixed $clientSecret;
    private mixed $accessToken;
    private string $envPath;

    public function __construct()
    {
        $dotenv = new Dotenv();
        if (dirname(__DIR__, 2) . '/.env.local') {
            $this->envPath = dirname(__DIR__, 2) . '/.env.local';
        } else {
            $this->envPath = dirname(__DIR__, 2) . '/.env';
        }
        $dotenv->load($this->envPath);

        $this->clientId = $_ENV['TWITCH_CLIENT_ID'];
        $this->clientSecret = $_ENV['TWITCH_CLIENT_SECRET'];
        $this->accessToken = $_ENV['TWITCH_ACCESS_TOKEN'];
    }

    public function getAccessToken()
    {
        $expiryTimestamp = $_ENV['TWITCH_TOKEN_EXPIRES_IN'];

        if (time() >= $expiryTimestamp) {
            $tokens = $this->refreshToken();
            return $tokens['access_token'];
        }

        return $this->accessToken;
    }
    public function refreshToken(): array
    {
        $url = 'https://id.twitch.tv/oauth2/token';

        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials'
        ];

        $options = [
            'http' => [
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === false) {
            throw new \Exception('Error fetching twitch client credentials token.');
        }

        $response = json_decode($result, true, 512, JSON_THROW_ON_ERROR);

        $this->updateEnvFile($response['access_token'], $response['expires_in']);

        return [
            'access_token' => $response['access_token'],
            'expires_in' => $response['expires_in']
        ];
    }

    public function updateEnvFile($newAccessToken, $expiresIn): void
    {
        $envContents = file($this->envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($envContents as &$line) {
            if (str_starts_with($line, 'TWITCH_ACCESS_TOKEN=')) {
                $line = 'TWITCH_ACCESS_TOKEN=' . $newAccessToken;
            }
            if (str_starts_with($line, 'TWITCH_TOKEN_EXPIRES_IN=')) {
                $line = 'TWITCH_TOKEN_EXPIRES_IN=' . $expiresIn;
            }

            if (str_starts_with($line, 'IGDB_AUTHORIZATION=')) {
                $line = 'IGDB_AUTHORIZATION="Bearer ' . $newAccessToken . '"';
            }
        }

        file_put_contents($this->envPath, implode(PHP_EOL, $envContents));
    }
}
