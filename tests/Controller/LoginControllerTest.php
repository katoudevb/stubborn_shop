<?php

namespace App\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

//Permet de verifier la connexion a un utilisateur
class LoginFormTest extends WebTestCase
{
    private $entityManager;
    private $passwordHasher;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->passwordHasher = $container->get(UserPasswordHasherInterface::class);
    }

    public function testUserCanLogin()
    {
        $client = static::createClient();

        // 1. Création d'un utilisateur de test dans la base
        $user = new User();
        $user->setEmail('testuser@example.com');
        $user->setName('Test User');
        $user->setIsVerified(true);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'password123')
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // 2. Accès à la page de login
        $crawler = $client->request('GET', '/login');

        // 3. Remplissage du formulaire
        $form = $crawler->selectButton('Connexion')->form([
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ]);

        // 4. Soumission du formulaire
        $client->submit($form);

        // 5. Vérification que la redirection vers la page sécurisée se fait
        $this->assertResponseRedirects('/client');

        $client->followRedirect();

        // 6. Vérifie que l'utilisateur est bien affiché sur la page
        $this->assertSelectorTextContains('body', 'testuser@example.com');
    }

    protected function tearDown(): void
    {
        // Supprime l'utilisateur de test pour ne pas polluer la BDD
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => 'test@example.com']);
        if ($user) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }

        parent::tearDown();
        $this->entityManager = null;
    }
}
