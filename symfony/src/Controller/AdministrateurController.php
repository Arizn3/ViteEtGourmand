<?php

namespace App\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Document\Stat;

// Contrôleur pour l'administrateur
final class AdministrateurController extends AbstractController
{
    // Données pour les statistiques
    #[Route('/administrateur', name: 'app_administrateur')]
    public function index(DocumentManager $dm, Request $request): Response
    {
        // Contôle d'accès
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Valeur filtre
        $periode = $request->query->get('periode') ?? date('Y-m');


        // Récupère un document via une période
        $stats = $dm->getRepository(Stat::class)->findBy([
            'periode' => $periode
        ]);

        $labels = [];
        $values = [];
        $revenues = [];
        $totalCA = 0;
        
        // Stockage des données dans un tableau
        foreach ($stats as $stat) {
            $labels[] = $stat->getMenu();
            $values[] = $stat->getTotalCommandes();
            $revenues[] = $stat->getChiffreAffaire();
            $totalCA += $stat->getChiffreAffaire();
        };

        return $this->render('administrateur/index.html.twig', [
            'labels' => $labels,
            'values' => $values,
            'revenues' => $revenues,
            'periode' => $periode,
            'totalCA' => $totalCA
        ]);
    }
}
