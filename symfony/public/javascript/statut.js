// DOMContentLoaded permet de charger le JS uniquement et strictement quand le HTML est prêt
document.addEventListener('DOMContentLoaded', function () {
    // Vérification de l'ID (Sécurité)
    const form = document.getElementById('form-statut');
    if (form) {
        // Écoute de la soumission d'une valeur
        form.addEventListener('submit', function (e) {
            // Vérification de la valeur choisie
            const statut = document.getElementById('statut-select').value;

            let message = null;

            // Popup d'alerte
            if (statut === 'Terminer') {
                message = "Confirmer la fin de la commande ?";
            } else if (statut === 'Annuler') {
                message = "Confirmer l'annulation ?";
            };

            // Annulation
            if (message && !confirm(message)) {
                e.preventDefault();
            }
        });
    };
});
