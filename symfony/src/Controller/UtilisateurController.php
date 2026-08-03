<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\CommandeModificationService;
use App\Form\UserChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommandeRepository;
use App\Repository\AvisRepository;
use App\Form\UtilisateurType;
use App\Form\CommandeType;
use App\Entity\Commande;
use App\Form\AvisType;
use App\Entity\Avis;


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

        // Récupération des commandes passées par l'utilisateur connecté, avec un filtre descendant.
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
        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($commande->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // Récupération des historiques du statut d'une commande avec un tri
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
        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($commande->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // La commande peut être annulée uniquement si elle n'a pas été prise en compte
        if ($commande->getStatut() === 'Votre commande va être prise en compte') {

            $menu = $commande->getMenu();
            $menu->setQttRestante(
                $menu->getQttRestante() + $commande->getNbPersonne()
            );

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
        CommandeModificationService $commandeModification,
        EntityManagerInterface $em,
        Commande $commande,
        Request $request,
    ): Response {

        // Contrôle d'utilisateur
        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($commande->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // Création d'un formulaire depuis CommandeType
        $form = $this->createForm(CommandeType::class, $commande, [
            'modification' => true
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $commandeModification->verifierDateModification($commande);
                $em->flush();
                return $this->redirectToRoute('app_utilisateur_commandes_en_cours', [
                    'id' => $commande->getId()
                ]);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('app_utilisateur_modifier_commande', [
                    'id' => $commande->getId()
                ]);
            }
        }

        return $this->render('utilisateur/modifier-commande.html.twig', [
            'form' => $form->createView(),
            'commande' => $commande,
        ]);
    }

    // Affichage des informations personnelle - Modification du mot de passe - Avis
    #[Route('/utilisateur/information-personnelle', name: 'app_utilisateur_information_personnelle')]
    public function informationPersonnelle(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        AvisRepository $avisRepo,
        CommandeRepository $commandeRepo
    ): Response {
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
        $form = $this->createForm(UserChangePasswordFormType::class);
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

    // Suppression du compte (soft delete)
    #[Route('/utilisateur/supprimer-mon-compte', name: 'app_utilisateur_supprimer_compte')]
    public function supprimerCompte(EntityManagerInterface $em, TokenStorageInterface $tokenStorage): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $utilisateur = $this->getUser();
        /** @var \App\Entity\Utilisateur $utilisateur */

        $tokenStorage->setToken(null);

        $utilisateur->setEmail('deleted_' . $utilisateur->getEmail());
        $utilisateur->setDeletedAt(new \DateTime());
        $em->flush();

        return $this->redirectToRoute('app_home');
    }

    // Ajout ou modification d'un avis
    #[Route('/utilisateur/avis', name: 'app_utilisateur_avis')]
    public function avis(
        Request $request,
        EntityManagerInterface $em,
        AvisRepository $avisRepo,
    ): Response {
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
        $this->denyAccessUnlessGranted('ROLE_USER');

        $utilisateur = $this->getUser();
        /** @var \App\Entity\Utilisateur $utilisateur */

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
