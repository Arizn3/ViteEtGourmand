<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Stat;

// Contrôleur pour l'administrateur
final class AdministrateurController extends AbstractController
{
    // Affichage des statistiques
    #[Route('/administrateur', name: 'app_administrateur')]
    public function index(DocumentManager $dm): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // dd($dm);

        // $stat = new Stat();
        // $stat->SetMenu('Menu A');
        // $stat->setTotalCommandes(5);
        // $stat->setChiffreAffaire(250);
        // $stat->setDate(new \DateTime());

        // $dm->persist($stat);
        // $dm->flush();

        return new Response('Ok');
    }
}
