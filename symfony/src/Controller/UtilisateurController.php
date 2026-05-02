<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Commande;
use App\Form\AvisType;
use App\Form\ChangePasswordFormType;
use App\Form\CommandeType;
use App\Form\UtilisateurType;
use App\Repository\AvisRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// Contrôleur pour l'espace utilisateur
final class UtilisateurController extends AbstractController
{
    // historique des commandes
    #[Route('/utilisateur/mes-commmandes', name: 'app_utilisateur_commandes')]
    public function commandes(CommandeRepository $commandeRepo): Response
    {
        // Contôle d'accès
        $this->denyAccessUnlessGranted('ROLE_USER');

        $utilisateur = $this->getUser();

        // Récupération des commandes en cours grâce à une requête personnaliser provenant de CommandeRepository
        $commandesEnCours = $commandeRepo->commandeEnCours($utilisateur);

        // Récupération des commandes passées de l'utilisateur connecté, avec un filtre descendant.
        $commandes = $commandeRepo->findBy(
            [
                'utilisateur' => $utilisateur,
                'statut' => 'Terminer'
            ],
            ['dateCmd' => 'DESC'],
        );

        return $this->render('utilisateur/commandes.html.twig', [
            'commandes' => $commandes,
            'commandeEnCours' => $commandesEnCours
        ]);
    }

    // Détail d'une commande
    #[Route('/utilisateur/commande-en-cours/{id}', name: 'app_utilisateur_commandes_en_cours')]
    public function detailCommande(Commande $commande): Response
    {
        // Contôle d'accès
        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($commande->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $historiques = $commande->getHistoriques()->toArray();
        usort($historiques, function ($a, $b) {
            return $a->getDate() <=> $b->getDate();
        });

        return $this->render('utilisateur/detail-commande.html.twig', [
            'commande' => $commande,
            'historiques' => $historiques
        ]);
    }

    // Annulation d'une commande (sous condition)
    #[Route('/utilisateur/{id}/annuler', name: 'app_utilisateur_annuler_commande')]
    public function FunctionName(Commande $commande, EntityManagerInterface $em)
    {
        // Contôle d'accès
        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($commande->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // La commande peut être annulée uniquement si elle n'a pas été prise en compte
        if ($commande->getStatut() === 'Votre commande va être prise en compte') {
            $commande->setStatut('Annuler');
            $em->flush();
        } else {
            $this->addFlash('error', 'Cette commande a déjà été prise en compte, elle ne peut plus être annulée.');
            return $this->redirectToRoute('app_utilisateur_commandes');
        }

        return $this->redirectToRoute('app_utilisateur_commandes');
    }

    // Modification d'une commande
    #[Route('/utilisateur/modifier-ma-commande/{id}', name: 'app_utilisateur_modifier_commande')]
    public function modifierCommande(
        Commande $commande,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        // Contôle d'accès
        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($commande->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // Création d'un formulaire depuis CommandeType
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $menu = $commande->getMenu();

            if ($commande->getNbPersonne() < $menu->getPersonneMini()) {
                $this->addFlash('error', 'Le nombre de boîtes à repas doit être au minimum requis pour le menu');
                return $this->redirectToRoute('app_utilisateur_modifier_commande', [
                    'id' => $commande->getId()
                ]);
            }

            $prixTotal = $menu->getPrixPersonne() * $commande->getNbPersonne();

            if ($commande->getNbPersonne() >= ($menu->getPersonneMini() + 5)) {
                $prixTotal *= 0.9;
            }

            $prixLivraison = 0;

            if (strtolower($commande->getVilleLivraison()) !== 'bordeaux') {
                // Simulation temporaire
                $distance = 10;
                $prixLivraison = 5 + ($distance * 0.59);
                // Prix final avec livraison
                $prixTotal += $prixLivraison;
            }

            // Utilisation des Setters (Modifie) de l'Entité Commande
            $commande->setPrixMenu($prixTotal);
            $commande->setPrixLivraison($prixLivraison);

            $em->flush();
            return $this->redirectToRoute('app_utilisateur_commandes', [
                'id' => $commande->getId()
            ]);
        }

        return $this->render('utilisateur/modifier-commande.html.twig', [
            'form' => $form->createView(),
            'commande' => $commande,
        ]);
    }

    // Affichage des informations personnelle, Modification du mot de passe, Avis
    #[Route('/utilisateur/information-personnelle', name: 'app_utilisateur_information_personnelle')]
    public function informationPersonnelle(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        AvisRepository $avisRepo,
        CommandeRepository $commandeRepo
    ): Response {
        // Contôle d'accès
        $this->denyAccessUnlessGranted('ROLE_USER');

        $utilisateur = $this->getUser();

        /** @var \App\Entity\Utilisateur $utilisateur */

        // Récupération de l'avis de l'utilisateur
        $avis = $avisRepo->findOneBy([
            'utilisateur' => $utilisateur,
        ]);

        $commandeTerminer = $commandeRepo->findOneBy([
            'utilisateur' => $utilisateur,
            'statut' => 'TERMINER'
        ]);

        // Modification du mot de passe
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($utilisateur, $newPassword);
            $utilisateur->setPassword($hashedPassword);

            $em->flush();

            return $this->redirectToRoute('app_utilisateur_information_personnelle');
        }

        return $this->render('utilisateur/information-personnelle.html.twig', [
            'form' => $form->createView(),
            'utilisateur' => $utilisateur,
            'avis' => $avis,
            'commandeTerminer' => $commandeTerminer
        ]);
    }

    // Ajouter ou modifier un avis
    #[Route('/utilisateur/avis', name: 'app_utilisateur_avis')]
    public function avis(
        Request $request,
        EntityManagerInterface $em,
        AvisRepository $avisRepo,
    ): Response {
        // Contôle d'accès
        $this->denyAccessUnlessGranted('ROLE_USER');

        $utilisateur = $this->getUser();
        /** @var \App\Entity\Utilisateur $utilisateur */

        $avis = $avisRepo->findOneBy([
            'utilisateur' => $utilisateur
        ]);

        if (!$avis) {
            $avis = new Avis();
            $avis->setUtilisateur($utilisateur);
        }

        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avis->setUtilisateur($utilisateur);
            $avis->setStatut('EN_ATTENTE');
            $em->persist($avis);
            $em->flush();

            return $this->redirectToRoute('app_utilisateur_information_personnelle');
        }

        return $this->render('utilisateur/avis-utilisateur.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Suppression d'un avis
    #[Route('/utilisateur/avis/{id}', name: 'app_utilisateur_supprimer_avis')]
    public function supprimerAvis(Avis $avis, EntityManagerInterface $em): Response
    {
        // Contôle d'accès
        $this->denyAccessUnlessGranted('ROLE_USER');

        $em->remove($avis);
        $em->flush();

        $this->addFlash('success', 'Votre avis a été supprimé');

        return $this->redirectToRoute('app_utilisateur_information_personnelle');
    }

    // Méthode pour la modification des informations personnelles
    #[Route('/utilisateur/information-personnelle/modifier', name: 'app_utilisateur_modifier_information_personnelle')]
    public function modifierInformation(Request $request, EntityManagerInterface $em): Response
    {
        // Contôle d'accès
        $this->denyAccessUnlessGranted('ROLE_USER');

        $utilisateur = $this->getUser();

        // Création d'un formulaire de puis UtilisateurType
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_utilisateur_information_personnelle');
        }

        return $this->render('utilisateur/modifier-information.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
