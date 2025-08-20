<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Psr\Log\LoggerInterface;

class RegistrationController extends AbstractController
{
    private MailerInterface $mailer;
    private LoggerInterface $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hachage du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($hashedPassword);

            // Génération du token d'activation
            $token = bin2hex(random_bytes(32));
            $user->setActivationToken($token);

            // Persistance
            $em->persist($user);
            $em->flush();

            // DEBUG : log + dump
            $this->logger->info('Nouvel utilisateur inscrit', [
                'email' => $user->getEmail(),
                'token' => $user->getActivationToken()
            ]);

            dump($user->getEmail(), $user->getActivationToken());

            // Envoi de l'email d'activation
            try {
                $this->mailer->send(
                    (new TemplatedEmail())
                        ->from('test@example.com') // plus sûr pour MailHog
                        ->to($user->getEmail())
                        ->subject('Activation de votre compte')
                        ->htmlTemplate('emails/activation.html.twig')
                        ->context([
                            'user' => $user,
                            'token' => $user->getActivationToken(),
                        ])
                );

                $this->logger->info('Email envoyé avec succès à ' . $user->getEmail());
            } catch (\Exception $e) {
                $this->logger->error('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
                dump($e->getMessage());
                die;
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
