<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route('/session/set', name: 'session_set')]
    public function set(): Response
    {
        $session = $this->container->get('session');
        $session->set('user', 'Kat');

        return new Response('Valeur en session: ' . $session->get('user'));
    }

    #[Route('/session/get', name: 'session_get')]
    public function get(): Response
    {
        $session = $this->container->get('session');
        $user = $session->get('user', 'inconnu');

        return new Response('Valeur en session: ' . $user);
    }
}
