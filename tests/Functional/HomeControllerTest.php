<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h2', 'Tendances');
    }

    public function testNewsletterSubscription(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr');

        $client->submitForm('newsletter_subscription', [
            'newsletter_subscription[email]' => ''
        ]);
    }
}