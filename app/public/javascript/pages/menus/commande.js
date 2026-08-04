// DOMContentLoaded permet de charger le JS uniquement et strictement quand le HTML est prêt
document.addEventListener('DOMContentLoaded', () => {

    const nb_personnes = document.getElementById('commande_nbPersonne');
    const date_prestation = document.getElementById('commande_datePrestation');
    const heure_livraison = document.getElementById('commande_heureLivraison');
    const adresse_livraison = document.getElementById('commande_adresseLivraison');
    const ville_livraison = document.getElementById('commande_villeLivraison');

    const erreur = document.getElementById('erreur');
    const prix = document.getElementById('prix');
    const prixTotalLivraison = document.getElementById('prixTotalLivraison');

    const form = document.getElementById('form_commande');

    if (!nb_personnes) return;

    // Les méthodes parse tranforme une chaîne de caractères en nombre
    const prixPersonne = parseFloat(nb_personnes.dataset.prix);
    const min = parseInt(nb_personnes.dataset.min);
    const max = parseInt(nb_personnes.dataset.max);

    let derniereVille = '';
    let prixLivraison = 0;

    // Fonction qui vérifie les champs, cette fonction est ensuite appeler en callback pour un évènement
    function verifierFormulaire() {
        const nb = parseInt(nb_personnes.value);

        if (!nb_personnes.value) {
            prix.textContent = '';
            prixTotalLivraison.textContent = '';
            return;
        }

        prix.textContent = '';

        // Calcule du prix dynamique
        let total = nb * prixPersonne;
        // Réduction du prix si la condition est respectée pour l'affichage dynamique du prix
        if (nb >= min + 5) {
            total *= 0.9;
        }

        // Majoration du prix en cas de livraison hors de la ville de Bordeaux
        if (ville_livraison.value.toLowerCase() !== 'bordeaux') {

            if (ville_livraison.value.trim() === '') {

                prix.textContent =
                    'Prix : ' +
                    total.toFixed(2) +
                    ' €';

                return;
            }

            if (derniereVille !== ville_livraison.value.trim()) {

                derniereVille = ville_livraison.value.trim();

                fetch(
                    calculLivraisonUrl +
                    '?ville=' +
                    encodeURIComponent(ville_livraison.value.trim())
                )

                    .then(response => {

                        if (!response.ok) {
                            throw new Error('Ville invalide');
                        }

                        return response.json();

                    })

                    .then(data => {

                        if (data.error) {
                            prixTotalLivraison.textContent = data.error;
                            return;
                        }

                        prixLivraison = parseFloat(data.prixLivraison);

                        prixTotalLivraison.textContent =
                            'Livraison : ' +
                            prixLivraison.toFixed(2) +
                            ' €';

                        let totalFinal = total + prixLivraison;

                        prix.textContent =
                            'Prix : ' +
                            totalFinal.toFixed(2) +
                            ' €';

                    });

            } else {

                let totalFinal = total + prixLivraison;

                prixTotalLivraison.textContent =
                    'Livraison : ' +
                    prixLivraison.toFixed(2) +
                    ' €';

                prix.textContent =
                    'Prix : ' +
                    totalFinal.toFixed(2) +
                    ' €';
            }

        } else if (ville_livraison.value.toLowerCase() === 'bordeaux') {

            prixLivraison = 0

            let totalFinal = total

            prixTotalLivraison.textContent =
                'Livraison : ' +
                0 +
                ' €';

            prix.textContent =
                'Prix : ' +
                totalFinal.toFixed(2) +
                ' €';

        }
    }

    // Évènement d'interception du submit (la validation) en cas de champ manquant
    form.addEventListener('submit', (e) => {
        const nb = parseInt(nb_personnes.value);
        if (
            // Conditions primaire pour l'envoie du formulaire
            !nb_personnes.value ||
            !date_prestation.value ||
            !heure_livraison.value ||
            !ville_livraison.value ||
            !adresse_livraison.value
        ) {
            // Bloque l'envoie du formulaire
            e.preventDefault();
            return;
        } else {
            const btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerText = "Envoi en cours...";
        }
    })

    // Évènements JS pour les érreurs de champs manquant pour la commande
    nb_personnes.addEventListener('input', verifierFormulaire);
    date_prestation.addEventListener('input', verifierFormulaire);
    heure_livraison.addEventListener('input', verifierFormulaire);
    ville_livraison.addEventListener('change', verifierFormulaire);
    adresse_livraison.addEventListener('input', verifierFormulaire);

    verifierFormulaire();
})