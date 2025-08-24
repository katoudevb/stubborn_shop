<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use Symfony\Component\Mailer\Test\Transport\InMemoryTransport;

class RegistrationControllerTest extends WebTestCase
{
    public function testUserRegistrationAndActivation()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        // 1️⃣ Préparer le transport mail en mémoire
        /** @var InMemoryTransport $transport */
        $transport = $container->get('mailer.transport.memory');
        $transport->reset();

        // 2️⃣ Soumettre le formulaire d'inscription
        $crawler = $client->request('GET', '/register');
        $form = $crawler->selectButton('S’inscrire')->form([
            'registration_form[email]' => 'test@example.com',
            'registration_form[plainPassword]' => 'Password123',
        ]);
        $client->submit($form);

        // Vérifier redirection après inscription
        $this->assertResponseRedirects('/some-success-page');
        $client->followRedirect();
        $this->assertSelectorTextContains('.flash-success', 'Inscription réussie');

        // 3️⃣ Vérifier qu’un mail a été envoyé
        $sentMessages = $transport->getSent();
        $this->assertCount(1, $sentMessages); // 1 mail envoyé

        $email = $sentMessages[0]->getMessage();
        $htmlBody = $email->getHtmlBody();
        $this->assertStringContainsString('Activation de votre compte', $email->getSubject());

        // 4️⃣ Extraire le lien d'activation depuis le mail
        preg_match('/https?:\/\/[^\s"]+/', $htmlBody, $matches);
        $this->assertNotEmpty($matches, 'Lien d’activation trouvé dans le mail');
        $activationUrl = str_replace('http://localhost', '', $matches[0]); // adapter si nécessaire

        // 5️⃣ Simuler la visite du lien d’activation
        $client->request('GET', $activationUrl);
        $this->assertResponseRedirects('/login'); // redirection après activation
        $client->followRedirect();
        $this->assertSelectorTextContains('.flash-success', 'Votre compte est activé avec succès');

        // 6️⃣ Vérifier en base que l’utilisateur est activé
        $user = $container->get('doctrine')->getRepository(User::class)->findOneByEmail('test@example.com');
        $this->assertNotNull($user);
        $this->assertTrue($user->isVerified(), 'L’utilisateur est bien activé');

        // 7️⃣ Nettoyage : supprimer l’utilisateur pour pouvoir relancer le test
        $entityManager = $container->get('doctrine')->getManager();
        $entityManager->remove($user);
        $entityManager->flush();
    }
}
