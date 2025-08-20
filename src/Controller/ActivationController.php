<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActivationController extends AbstractController
{
    #[Route('/activate/{token}', name: 'app_activate')]
    public function activate(string $token, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $user = $userRepository->findOneBy(['activationToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Token invalide ou compte déjà activé.');
            return $this->redirectToRoute('app_login');
        }

        $user->setIsVerified(true);
        $user->setActivationToken(null);
        $em->flush();

        $this->addFlash('success', 'Votre compte est maintenant activé ! Vous pouvez vous connecter.');
        return $this->redirectToRoute('app_login');
    }
}
