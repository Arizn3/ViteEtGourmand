// DOMContentLoaded permet de charger le JS uniquement et strictement quand le HTML est prêt
document.addEventListener('DOMContentLoaded', () => {

    const input = document.getElementById('nb_personnes');
    const erreur = document.getElementById('erreur');
    const prix = document.getElementById('prix');

    if (!input) return;

    // Les méthodes parse tranforme une chaîne de caractères en nombre
    const prixPersonne = parseFloat(input.dataset.prix);
    const min = parseInt(input.dataset.min);

    // Événement JS pour l'affichage dynamique du prix
    input.addEventListener('input', () => {
        const nb = parseInt(input.value);

        if (isNaN(nb)) return;

        if (nb < min) {
            erreur.textContent = "Minimum : " + min + " personnes";
            prix.textContent = "";
            return;
        }

        erreur.textContent = "";

        let total = nb * prixPersonne;

        if (nb >= min + 5) {
            total *= 0.9;
        }

        prix.textContent = "Prix total : " + total.toFixed(2) + " €";
    });
});