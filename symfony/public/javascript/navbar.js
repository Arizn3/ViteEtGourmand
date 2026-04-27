// DOMContentLoaded permet de charger le JS uniquement et strictement quand le HTML est prêt
document.addEventListener('DOMContentLoaded', function () {

    // Ouverture des dropdown
    document.addEventListener('click', function(e) {
        const btnNavAdmin = document.querySelector('.btnNavAdmin');
        const btnNavEmploye = document.querySelector('.btnNavEmploye');
        if (btnNavAdmin.contains(e.target)) {
            const menuAdmin = document.querySelector('.lienAdmin');
            menuAdmin.style.display = (menuAdmin.style.display === 'block') ? 'none' : 'block';
        }
        if (btnNavEmploye.contains(e.target)) {
            const menuEmploye = document.querySelector('.lienEmploye');
            menuEmploye.style.display = (menuEmploye.style.display === 'block') ? 'none' : 'block';
        }
    })

    // fermeture des dropdown
    document.addEventListener('click', function (e) {
        const menuAdmin = document.querySelector('.navAdmin');
        const menuEmploye = document.querySelector('.navEmploye');
        if (!menuAdmin.contains(e.target)) {
            document.querySelector('.lienAdmin').style.display = 'none';
        }
        if (!menuEmploye.contains(e.target)) {
            document.querySelector('.lienEmploye').style.display = 'none';
        }
    })

});