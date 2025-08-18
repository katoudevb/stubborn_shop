<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

final class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        VerifyEmailHelperInterface $verifyEmailHelper,
        MailerInterface $mailer
    ): Response {
        $user = new User();
        $user->setIsVerified(false); // initialisation

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            // Génération du lien de confirmation
            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            // Préparation de l’email
            $email = (new TemplatedEmail())
                ->from('no-reply@ton-domaine.com')
                ->to($user->getEmail())
                ->subject('Confirmez votre adresse email')
                ->htmlTemplate('emails/confirmation.html.twig')
                ->context([
                    'signedUrl' => $signatureComponents->getSignedUrl(),
                    'expiresAt' => $signatureComponents->getExpiresAt(),
                    'user' => $user,
                ]);

            $mailer->send($email);

            $this->addFlash('success', 'Un email de confirmation a été envoyé. Merci de vérifier votre boîte de réception.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        VerifyEmailHelperInterface $verifyEmailHelper,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $authenticator
    ): Response {
        $id = $request->get('id');

        if (!$id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->redirectToRoute('app_register');
        }

        try {
            $verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
            $user->setIsVerified(true);
            $entityManager->flush();

            $this->addFlash('success', 'Votre adresse email a été vérifiée avec succès !');

            // Authentification automatique et redirection vers la page d’accueil
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Le lien de vérification est invalide ou a expiré.');
            return $this->redirectToRoute('app_register');
        }
    }
}
