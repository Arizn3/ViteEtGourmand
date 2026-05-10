// DOMContentLoaded permet de charger le JS uniquement et strictement quand le HTML est prêt
document.addEventListener('DOMContentLoaded', function () {

    const photo = document.getElementById('photo');

    document.querySelectorAll('.photos').forEach(img => {

        img.addEventListener('click', () => {
            photo.src = img.src;
        });

    });

});