<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

final class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminDashboard(): Response
    {
        return $this->render('dashboard/admin.html.twig');
    }

    #[Route('/client', name: 'client_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function clientDashboard(): Response
    {
        return $this->render('dashboard/client.html.twig');
    }
}
