// DOMContentLoaded permet de charger le JS uniquement et strictement quand le HTML est prêt
document.addEventListener('DOMContentLoaded', () => {

    const nb_personnes = document.getElementById('nb_personnes');
    const date_prestation = document.getElementById('date_prestation');
    const heure_livraison = document.getElementById('heure_livraison');
    const adresse_livraison = document.getElementById('adresse_livraison');
    const erreur = document.getElementById('erreur');
    const prix = document.getElementById('prix');
    const form = document.getElementById('form_commande');

    if (!nb_personnes) return;

    // Les méthodes parse tranforme une chaîne de caractères en nombre
    const prixPersonne = parseFloat(nb_personnes.dataset.prix);
    const min = parseInt(nb_personnes.dataset.min);

    // Fonction qui vérifie les champs, cette fonction est ensuite appeler en callback pour un évènement
    function verifierFormulaire() {
        const nb = parseInt(nb_personnes.value);

        if (nb < min) {
            erreur.textContent = "👉 Un minimum de " + min + " personnes est demandé pour la commande du menu";
            prix.textContent = "";
            return;
        }
        if (!date_prestation.value) {
            erreur.textContent = "👉 Une date de livraison est nécessaire";
            return;
        }
        if (!heure_livraison.value) {
            erreur.textContent = "👉 Une heure de livraison est nécessaire";
            return;
        }
        if (!adresse_livraison.value) {
            erreur.textContent = "👉 Une adresse de livraison est nécessaire";
            return;
        }

        erreur.textContent = "";

        // Calcule du prix dynamique
        let total = nb * prixPersonne;

        // Réduction du prix si la condition est respectée pour l'affichage dynamique du prix
        if (nb >= min + 5) {
            total *= 0.9;
        }

        // Affichage du prix dynamique
        prix.textContent = "Prix total : " + total.toFixed(2) + " €";
    }

    // Évènements JS pour les érreurs de champs manquant pour la commande
    nb_personnes.addEventListener('input', verifierFormulaire);
    date_prestation.addEventListener('input', verifierFormulaire);
    heure_livraison.addEventListener('input', verifierFormulaire);
    adresse_livraison.addEventListener('input', verifierFormulaire);

    // Évènement d'interception du submit (la validation) en cas de champ manquant
    form.addEventListener('submit', (e) => {
        const nb = parseInt(nb_personnes.value);

        if (
            // Conditions
            isNaN(nb) ||
            nb < min ||
            !date_prestation.value ||
            !heure_livraison.value ||
            !adresse_livraison.value
        ) {
            // Bloque l'envoie du formulaire
            e.preventDefault();
            alert('⚠️ Veuillez remplir tous les champs correctement avant de valider la commande');
            return;
        } else {
            alert('✅ Votre commande à bien été enregistrer, un email vous à été envoyer');
        }
    })
});