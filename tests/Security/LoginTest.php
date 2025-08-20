<?php

namespace App\Tests\Security;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginTest extends WebTestCase
{
    private function createUser(string $email, string $password): User
    {
        self::bootKernel();
        $em = static::getContainer()->get('doctrine')->getManager();
        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $user = new User();
        $user->setEmail($email);
        $user->setName('Test User');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($passwordHasher->hashPassword($user, $password));

        $em->persist($user);
        $em->flush();

        return $user;
    }

    public function testLoginWithValidCredentials(): void
    {
        $client = static::createClient();
        $this->createUser('test@example.com', 'password123');

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $client->submit($form);

        // Vérifie la redirection vers l’accueil (app_home → "/")
        $this->assertResponseRedirects('/');
        $client->followRedirect();

        // Vérifie que le lien de déconnexion est affiché
        $this->assertSelectorExists('a[href="/logout"]');
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form([
            'email' => 'wrong@example.com',
            'password' => 'wrongpass',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/login');
        $client->followRedirect();

        // Vérifie que l’alerte d’erreur est affichée
        $this->assertSelectorExists('.alert.alert-danger');
    }
}
