<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SessionTest extends WebTestCase
{
    public function testSessionPersistsBetweenPages(): void
    {
        $client = static::createClient();

        // 1️⃣ On définit la valeur en session
        $client->request('GET', '/session/set');
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Valeur en session: Kat', $client->getResponse()->getContent());

        // 2️⃣ On récupère la valeur sur une autre page
        $client->request('GET', '/session/get');
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Valeur en session: Kat', $client->getResponse()->getContent());
    }
}
