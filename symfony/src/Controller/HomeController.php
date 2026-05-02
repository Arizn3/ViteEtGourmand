<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AvisRepository;

// Contrôleur pour la page d'accueil
final class HomeController extends AbstractController
{
    // Page d'accueil
    #[Route('/home', name: 'app_home')]
    public function index(AvisRepository $avisRepo): Response
    {

        $avis = $avisRepo->findBy(['statut' => 'VALIDE']);

        return $this->render('home/index.html.twig', [
            'avis' => $avis,
        ]);
    }

    // Conditions générale et mention légales (FOOTER)
    #[Route('/home/conditions-générale-et-mention-légales', name: 'app_home_conditions_mention')]
    public function condition(): Response
    {

        return $this->render('/home/conditions-mention.html.twig');
    }
}
