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

    // Fonction qui vérifie les champs, cette fonction est ensuite appeler en callback pour un évènement
    function verifierFormulaire() {
        const nb = parseInt(nb_personnes.value);

        if (isNaN(nb) || nb < min) {
            erreur.textContent = '👉 Un minimum de ' + min + ' personnes est demandé pour la commande du menu';
            prix.textContent = '';
            return;
        }
        if (isNaN(nb) || nb > max) {
            erreur.textContent = '👉 Un maximum de ' + max + ' boîte à repas disponible pour ce menu';
            prix.textContent = '';
            return;
        }
        if (date_prestation && date_prestation.value) {
            const prestationDate = new Date(date_prestation.value);
            const today = new Date();

            const minDate = new Date();
            minDate.setDate(today.getDate() + 7);

            if (prestationDate < minDate) {
                erreur.textContent = '👉 La date de livraison doit être au minimum dans 7 jours';
                return;
            }
        }
        if (!date_prestation.value) {
            erreur.textContent = '👉 Une date de livraison est nécessaire';
            return;
        }
        if (!heure_livraison.value) {
            erreur.textContent = '👉 Une heure de livraison est nécessaire';
            return;
        }
        if (!ville_livraison.value) {
            erreur.textContent = '👉 Veuillez indiquer une ville';
            return
        }
        if (!adresse_livraison.value) {
            erreur.textContent = '👉 Une adresse de livraison est nécessaire';
            return;
        }

        erreur.textContent = '';

        // Calcule du prix dynamique
        let total = nb * prixPersonne;
        // Réduction du prix si la condition est respectée pour l'affichage dynamique du prix
        if (nb >= min + 5) {
            total *= 0.9;
        }

        let prixLivraison = 0;
        // Majoration du prix en cas de livraison hors de la ville de Bordeaux
        if (ville_livraison.value.toLowerCase() !== 'bordeaux') {

            if (ville_livraison.value.trim() === '') {
                return;
            }

            fetch(calculLivraisonUrl +
                '?ville=' +
                encodeURIComponent(ville_livraison.value)
            )

                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        prixTotalLivraison.textContent = data.error;
                        return;
                    }

                    prixLivraison = data.prixLivraison;

                    prixTotalLivraison.textContent = 'Frais de livraison : ' +
                        prixLivraison.toFixed(2) + ' €';

                    let totalFinal = total + prixLivraison;

                    prix.textContent = 'Prix Total : ' +
                        totalFinal.toFixed(2) + ' €';
                });
        } else {
            prixTotalLivraison.textContent = 'Livraison gratuite sur Bordeaux';

            prix.textContent = 'Prix total : ' +
                total.toFixed(2) + ' €';
        }
    }

    // Évènements JS pour les érreurs de champs manquant pour la commande
    nb_personnes.addEventListener('input', verifierFormulaire);
    date_prestation.addEventListener('input', verifierFormulaire);
    heure_livraison.addEventListener('input', verifierFormulaire);
    ville_livraison.addEventListener('input', verifierFormulaire);
    adresse_livraison.addEventListener('input', verifierFormulaire);

    // Évènement d'interception du submit (la validation) en cas de champ manquant
    form.addEventListener('submit', (e) => {
        const nb = parseInt(nb_personnes.value);
        if (
            // Conditions primaire pour l'envoie du formulaire
            isNaN(nb) ||
            nb < min ||
            !date_prestation.value ||
            !heure_livraison.value ||
            !ville_livraison.value ||
            !adresse_livraison.value
        ) {
            // Bloque l'envoie du formulaire
            e.preventDefault();
            alert('⚠️ Veuillez remplir tous les champs correctement avant de valider la commande');
            return;
        } else {
            const btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerText = "Envoi en cours...";
        }
    })
})