<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\AdminEmployeService;
use App\Entity\Utilisateur;
use App\Document\Stat;

// Contrôleur pour l'administrateur
final class AdministrateurController extends AbstractController
{

    // Données pour les statistiques
    #[Route('/administrateur/statistiques', name: 'app_administrateur_stat')]
    public function index(DocumentManager $dm, Request $request): Response
    {
        // Contôle d'accès
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupération de la valeur du filtre
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

    // Création d'un compte employé et affichage de la liste
    #[Route('/administrateur/employe/nouveau', name: 'app_admin_nouveau_employe')]
    public function compteEmploye(
        AdminEmployeService $AdminEmployeService,
        EntityManagerInterface $em,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupération des comptes employés actifs
        $compte = $em->getRepository(Utilisateur::class)->findBy([
            'role' => 2,
            'deletedAt' => null
        ]);

        if ($request->isMethod('POST')) {
            try {
                $plainPassword = $AdminEmployeService->creerCompteEmploye(
                    $request->request->get('email'),
                    $request->request->get('prenom'),
                    $request->request->get('nom'),
                );
                $this->addFlash(
                    'success',
                    'Compte employé crée, veuillez noter le mot de passe avant de quitter la page : ' . $plainPassword
                );
                return $this->redirectToRoute(
                    'app_admin_nouveau_employe'
                );
            } catch (\RuntimeException $e) {
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
            return $this->redirectToRoute('app_admin_nouveau_employe');
        };

        return $this->render('administrateur/nouveau-employe.html.twig', [
            'compte' => $compte,
        ]);
    }

    // Suppression (soft delete) d'un compte employé
    #[Route('/administrateur/employe/supprimer/{id}', name: 'app_suppression_employe')]
    public function supprimerEmploye(Utilisateur $utilisateur, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $utilisateur->setEmail('deleted_' . $utilisateur->getEmail());
        $utilisateur->setDeletedAt(new \DateTime());

        $em->flush();

        $this->addFlash('supp', 'Compte supprimer');
        return $this->redirectToRoute('app_admin_nouveau_employe');
    }
}
