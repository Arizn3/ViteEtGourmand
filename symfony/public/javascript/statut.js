// DOMContentLoaded permet de charger le JS uniquement et strictement quand le HTML est prêt
document.addEventListener('DOMContentLoaded', function () {
    // Vérification de l'existence du formulaire (sécurité)
    const form = document.getElementById('form-statut');
    if (form) {

        // Vérification de la valeur choisie
        const statut = document.getElementById('statut-select');
        // Element HTML textarea
        const textarea = document.getElementById('message_email');

        textarea.style.display = 'none';

        // Écoute du choix du statut de la commande
        statut.addEventListener('change', function () {
            if (this.value === 'Annuler') {
                textarea.style.display = 'block';
            } else {
                textarea.style.display = 'none';
            };
        });

        // Écoute de la soumission d'une valeur
        form.addEventListener('submit', function (e) {
            // Vérification de la valeur choisie
            const statut = document.getElementById('statut-select').value;
            // Motif d'annulation
            const motif = document.getElementById('message_email').value;

            let message = null;

            // Popup d'alerte
            if (statut === 'Terminer') {
                message = "Confirmer la fin de la commande ?";
            } else if (statut === 'Annuler' && !motif) {
                message = "Indiquez un motif d'annulation";
                e.preventDefault();
            } else if (statut === 'Annuler' && motif) {
                message = "Confirmer l'annulation ?";
            }

            // Annulation
            if (message && !confirm(message)) {
                e.preventDefault();
            };
        });
    };
});
