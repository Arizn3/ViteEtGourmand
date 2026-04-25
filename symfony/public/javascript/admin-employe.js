// DOMContentLoaded permet de charger le JS uniquement et strictement quand le HTML est prêt
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.btn-supression').forEach(
        function (bouton) {

            if (bouton.dataset.listener) return;
            bouton.dataset.listener = "true";

            bouton.addEventListener('click', function (e) {
                const confirmation = confirm('Supprimer le compte ?');

                if (!confirmation) {
                    // Bloque la requête de suppression
                    e.preventDefault();
                };
            });
        }
    );
});