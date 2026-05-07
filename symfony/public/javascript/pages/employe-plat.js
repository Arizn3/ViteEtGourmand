// DOMContentLoaded permet de charger le JS uniquement et strictement quand le HTML est prêt
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.btn-supression').forEach(
        function (bouton) {
            bouton.addEventListener('click', function (e) {
                const confirmation = confirm('Supprimer le plat ?');

                if (!confirmation) {
                    // Bloque la requête de suppression
                    e.preventDefault();
                };
            });
        }
    );

    document.querySelectorAll('.btn-photo').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const nomPhoto = this.dataset.photo;
            const div = document.getElementById('photo-div')
            const img = div.querySelector('img');

            img.src = '/uploads/plats/' + nomPhoto;

            if (div.style.display === 'none') {
                div.style.display = 'block';
            };
        });
    });

});