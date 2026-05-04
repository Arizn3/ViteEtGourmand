<?php

namespace App\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Document\Stat;
use App\Entity\Utilisateur;
use App\Repository\RoleRepository;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// Contrôleur pour l'administrateur
final class AdministrateurController extends AbstractController
{
    // Données pour les statistiques
    #[Route('/administrateur/statistiques', name: 'app_administrateur_stat')]
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

    // Création d'un compte employé et affichage de la liste
    #[Route('/administrateur/employe/nouveau', name: 'app_admin_nouveau_employe')]
    public function compteEmploye(
        EntityManagerInterface $em,
        Request $request,
        MailerInterface $mailer,
        RoleRepository $roleRepository,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Donnée employé
        $compte = $em->getRepository(Utilisateur::class)->findBy([
            'role' => 2,
            'deletedAt' => null
            ]);

        // Creation d'un nouveau compte employé
        // Nouvelle instance de l'Entity Utilisateur
        $user = new Utilisateur();
        $plainPassword = null;

        if ($request->isMethod('POST')) {

            // Récuération des données depuis le fichier Twig
            $email = $request->request->get('email');
            $prenom = $request->request->get('prenom');
            $nom = $request->request->get('nom');

            // Erreur en cas de doublon d'email en base
            $existeEmail = $em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
            if ($existeEmail) {
                $this->addFlash('error', 'Email déjà utilisé');
                return $this->redirectToRoute('app_admin_nouveau_employe');
            };

            // Création d'un mot de passe aléatoire
            $plainPassword = bin2hex(random_bytes(4));
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);

            $user->setEmail($email);
            $user->setPassword($hashedPassword);
            $user->setPrenom($prenom);
            $user->setNom($nom);
            // Un compte employé ne contient pas d'adresse, ni de téléphone mais
            // ces données ne peuvent pas être vide en base.
            $user->setAdresse('UserEmp');
            $user->setTelephone('0000000000');
            $user->setCreatedAt(new \DateTime());

            // ROLE_EMPLOYE définit
            $roleUser = $roleRepository->findOneBy(['description' => 'ROLE_EMPLOYE']);
            $user->setRole($roleUser);

            // Persistance des données
            $em->persist($user);
            $em->flush();

            // Email
            $emailEmploye = (new Email())
                ->from('Vite & Gourmand <33vitegourmand@gmail.com>')
                ->to($email)
                ->subject('Création de votre compte employé')
                ->text("Bonjour,

Un compte empoyé a été créé pour vous.

Merci de contacter l'administrateur pour récupérer votre mot de passe.

L'équipe Vite & Gourmand
                ");

            $mailer->send($emailEmploye);

            $this->addFlash('success', 'Compte employé crée, veuillez noter le mot de passe avant de quitter la page : ' . $plainPassword);

            return $this->redirectToRoute('app_admin_nouveau_employe');
        };

        return $this->render('administrateur/nouveau-employe.html.twig', [
            'compte' => $compte,
        ]);
    }

    // Suppression (soft delete) d'un compte employé
    #[Route('/administrateur/employe/supprimer/{id}', name:'app_suppression_employe')]
    public function supprimerEmploye(Utilisateur $utilisateur, EntityManagerInterface $em): Response
    {
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $utilisateur->setEmail('deleted_' . $utilisateur->getEmail());
        $utilisateur->setDeletedAt(New \DateTime());

        $em->flush();

        $this->addFlash('supp', 'Compte supprimer');
        return $this->redirectToRoute('app_admin_nouveau_employe');
    }
}
