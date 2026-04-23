<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Contrôleur pour l'administrateur
final class AdministrateurController extends AbstractController
{
    // Affichage des statistiques
    #[Route('/administrateur', name: 'app_administrateur')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return new Response('Ok');
    }
}
