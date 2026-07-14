<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AvisRepository;

// Contrôleur pour la page d'accueil
final class HomeController extends AbstractController
{

    #[Route('/', name: 'app_root')]
    public function root(): Response
    {
        return $this->redirectToRoute('app_home');
    }

    // Page d'accueil
    #[Route('/accueil', name: 'app_home')]
    public function index(AvisRepository $avisRepo): Response
    {

        // Récupération des données d'Avis
        $avis = $avisRepo->findBy(['statut' => 'VALIDE']);

        return $this->render('home/index.html.twig', [
            'avis' => $avis,
        ]);
    }

    // Onglet Conditions Générale de Vente (CVG) et Mention Légales
    #[Route('/home/conditions-générale-et-mention-légales', name: 'app_home_conditions_mention')]
    public function condition(): Response
    {
        return $this->render('/home/conditions-mention.html.twig');
    }

    // Onglet Notre Histoire
    #[Route('/home/notre-histoire', name: 'app_home_histoire')]
    public function histoire(): Response
    {
        return $this->render('/home/notre-histoire.html.twig');
    }
    
}