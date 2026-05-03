// DOMContentLoaded permet de charger le JS uniquement et strictement quand le HTML est prêt
document.addEventListener('DOMContentLoaded', function () {

    const btnSuppression = document.getElementById('btnSuppression');

    btnSuppression.addEventListener('click', function (e) {
        const confirmation = confirm('Supprimer mon compte ?');

        if (!confirmation) {
            // Bloque la requête de suppression
            e.preventDefault();
        };
    });

});
