<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CommandeRepository;

// Controller pour l'espace employé
final class EmployeController extends AbstractController
{
    #[Route('/employe', name: 'app_employe')]
    public function index(CommandeRepository $CommandeRepo): Response
    {
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Récupération des données Commande en base
        $Commandes = $CommandeRepo->findAll();

        return $this->render('employe/index.html.twig', [
            'commandes' => $Commandes
        ]);
    }
}
