<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

class VerifyEmailController extends AbstractController
{
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        VerifyEmailHelperInterface $verifyEmailHelper,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        FormLoginAuthenticator $authenticator
    ): Response {
        $id = $request->get('id');

        if (!$id) {
            throw $this->createNotFoundException('Aucun identifiant utilisateur fourni.');
        }

        // Recherche de l'utilisateur par ID
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        // Validation du lien signé
        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail()
            );
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // Activation du compte
        $user->setIsVerified(true);
        $entityManager->flush();

        // Connexion automatique de l’utilisateur
        $userAuthenticator->authenticateUser(
            $user,
            $authenticator,
            $request
        );

        $this->addFlash('success', 'Votre adresse email a bien été confirmée et vous êtes connecté.');

        // Redirection vers la page d’accueil
        return $this->redirectToRoute('app_home');
    }
}
