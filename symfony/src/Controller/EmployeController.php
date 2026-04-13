<?php

namespace App\Controller;

use App\Entity\Avis;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CommandeRepository;
use App\Repository\AvisRepository;
use App\Entity\Commande;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

// Controller pour l'espace employé
final class EmployeController extends AbstractController
{
    // Affichage des commandes
    #[Route('/employe', name: 'app_employe')]
    public function index(CommandeRepository $CommandeRepo, Request $request): Response
    {
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Variable en cas de filtre (commande active)
        $idFilter = $request->query->get('idFilter');
        $emailFilter = $request->query->get('email');
        $statutFilter = $request->query->get('statut');

        // Filtre et affichage des commandes actives
        $query = $CommandeRepo->createQueryBuilder('c')
            ->join('c.utilisateur', 'u');

        if ($idFilter) {
            $query->andWhere('c.id = :id')
                ->setParameter('id', $idFilter);
        }
        if ($emailFilter) {
            $query->andWhere('u.email LIKE :email')
                ->setParameter('email', '%' . $emailFilter . '%');
        }
        if ($statutFilter) {
            $query->andWhere('c.statut = :statut')
                ->setParameter('statut', $statutFilter);
        }

        // Récupération des commandes terminée ou annulée grâce au paramètre ->setParameter()
        $commandesTerminees = $CommandeRepo->createQueryBuilder('c')
            ->where('c.statut IN (:statuts)')
            ->setParameter('statuts', ['Terminer', 'Annuler']);

        // Variable récupérer en cas de filtre (commande terminer ou annuler)
        $idTerminer = $request->query->get('idTerminer');
        $emailTerminer = $request->query->get('emailTerminer');
        $statutTerminer = $request->query->get('statutTerminer');

        // Filtre pour les commandes Terminer ou Annuler
        $queryTerminer = $CommandeRepo->createQueryBuilder('c')
            ->join('c.utilisateur', 'u')
            ->where('c.statut IN (:statuts)')
            ->setParameter('statuts', ['Terminer', 'Annuler']);

        if ($idTerminer) {
            $queryTerminer->andWhere('c.id = :id')
                ->setParameter('id', $idTerminer);
        }
        if ($emailTerminer) {
            $queryTerminer->andWhere('u.email LIKE :email')
                ->setParameter('email', '%' . $emailTerminer . '%');
        }
        if ($statutTerminer) {
            $queryTerminer->andWhere('c.statut LIKE :statut')
                ->setParameter('statut', '%' . $statutTerminer . '%');
        }

        $commandes = $query->getQuery()->getResult();
        $commandesTerminees = $queryTerminer->getQuery()->getResult();

        // Nombre des commandes actives pour Twig
        $commandeActives = array_filter($commandes, function ($c) {
            return !in_array($c->getStatut(), ['Terminer', 'Annuler']);
        });

        return $this->render('employe/index.html.twig', [
            'commandes' => $commandes,
            'commandesTerminees' => $commandesTerminees,
            'commandesActives' => $commandeActives
        ]);
    }

    // Affichage de l'adresse de livraison d'une commande
    #[Route('/employe/adresse/{id}', name: 'app_adresse_livraison')]
    public function adresseLivraison(Commande $commande): Response
    {
        // Contôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Récupération de l'adresse de la livraison
        return $this->render('employe/adresse.html.twig', [
            'commande' => $commande
        ]);
    }

    // Affichage d'information sur le client d'une commande
    #[Route('/employe/utilisateur/{id}', name: 'app_utilisateur')]
    public function detailUtilisateur(Utilisateur $utilisateur): Response
    {
        // Contôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Récupération des informations sur le client
        return $this->render('employe/utilisateur.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    // Page de changement du statut d'une commande
    #[Route('/employe/statut/{id}', name: 'app_commande_statut')]
    public function changementStatut(
        Commande $commande,
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer,
    ): Response {
        // Contôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Récupération du statut choisi par l'employé
        // La base de données est ensuite mise à jour
        if ($request->isMethod('POST')) {
            $statut = $request->request->get('statut');

            // Sécurité des statuts possible
            $statutPossible = [
                'Votre commande a été prise en compte',
                'Votre commande est en préparation',
                'En cours de livraison',
                'Commande livrée',
                'En attente du retour de matériel',
                'Terminer',
                'Annuler'
            ];
            if (!in_array($statut, $statutPossible)) {
                throw new \Exception('Statut Invalide');
            }

            // Système de mail en cas d'annulation d'une commande
            if ($statut === 'Annuler') {

                // Récupération du message de l'input (Twig)
                $messageEmail = $request->request->get('message_email');

                if (empty($messageEmail)) {
                    $messageEmail = 'Aucune précision fournie';
                }

                $email = (new Email())
                    ->from('Vite & Gourmand <33vitegourmand@gmail.com>')
                    ->to($commande->getUtilisateur()->getEmail())
                    ->subject('Annulation de votre commmande')
                    ->text('Bonjour,
            
Votre commande a été annulée pour les raisons suivantes :

' . $messageEmail . '

Nous restons à votre disposition, cordialement.
L\'équipe Vite & Gourmand
                ');

                $mailer->send($email);
            }

            // Modification et persistance de la donnée
            $commande->setStatut($statut);
            $em->flush();

            return $this->redirectToRoute('app_employe', $request->query->all());
        }

        return $this->render('employe/statut.html.twig', [
            'commande' => $commande
        ]);
    }

    // liste des avis utilisateurs
    #[Route('/employe/avis', name: 'app_employe_avis')]
    public function avisUtilisateur(AvisRepository $avisRepo): Response
    {
        // Contôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        $avis = $avisRepo->findBy(['statut' => 'EN_ATTENTE']);

        return $this->render('employe/avis.html.twig', [
            'avis' => $avis,
        ]);
    }

    ## Les deux fonctions suivantes sépare la validation et l'annulation des avis, pour les utiliser,
    ## il faut d'abord enlever le paramètre dynamique dans le fichier Twig au niveau des liens 'action: '***''
    ## et changer l'URL du lien en 'app_avis_action' pour ensuite commenter la fonction actionAvis.

    # // Function de validation d'un avis
    # #[Route('/employe/avis/valider/{id}', name: 'app_avis_valider')]
    # public function validerAvis(Avis $avis, EntityManagerInterface $em): Response
    # {
    #     // Contrôle d'accès
    #     $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

    #     // Appel du Setter de Avis
    #     $avis->setStatut('REFUSER');
    #     $em->flush();

    #     return $this->redirectToRoute('app_employe_avis');
    # }

    # // Function d'annuation d'un avis
    # #[Route('/employe/avis/refuser/{id}', name: 'app_avis_refuser')]
    # public function refuserAvis(Avis $avis, EntityManagerInterface $em): Response
    # {
    #     // Contrôle d'accès
    #     $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

    #     // Appel du Setter de Avis
    #     $avis->setStatut('VALIDE');
    #     $em->flush();

    #     return $this->redirectToRoute('app_employe_avis');
    # }

    // function gestion des avis
    #[Route('/employe/avis/{id}/{action}', name: 'app_avis_action')]
    public function actionAvis(Avis $avis, string $action, EntityManagerInterface $em): Response
    {
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        if ($action === 'VALIDE') {
            $avis->setStatut('VALIDE');
        } elseif ($action === 'REFUSER') {
            $avis->setStatut('REFUSER');
        } else {
            throw $this->createNotFoundException();
        }

        // Persistance des donnnées en base
        $em->flush();

        return $this->redirectToRoute('app_employe_avis');
    }

    // Historique des avis
    #[Route('/employe/avis/historique', name: 'app_historique_avis')]
    public function avisHistorique(AvisRepository $avisRepo): Response
    {
        // Contrôle d'accès
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // On récupère tous les avis
        $avis = $avisRepo->findBy(['statut' => ['VALIDE', 'REFUSER']]);

        return $this->render('employe/historique.html.twig', [
            'avis' => $avis,
        ]);
    }
}
