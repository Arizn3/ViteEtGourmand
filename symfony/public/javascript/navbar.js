// DOMContentLoaded permet de charger le JS uniquement et strictement quand le HTML est prêt
document.addEventListener('DOMContentLoaded', function () {

    // Ouverture des dropdown
    document.addEventListener('click', function (e) {
        const btnNavUtilisateur = document.querySelector('.btnNavUtilisateur');
        if (btnNavUtilisateur.contains(e.target)) {
            const menuUtilisateur = document.querySelector('.lienUtilisateur');
            menuUtilisateur.style.display = (menuUtilisateur.style.display === 'block') ? 'none' : 'block';
        }
    })

    document.addEventListener('click', function (e) {
        const btnNavEmploye = document.querySelector('.btnNavEmploye');
        if (btnNavEmploye.contains(e.target)) {
            const menuEmploye = document.querySelector('.lienEmploye');
            menuEmploye.style.display = (menuEmploye.style.display === 'block') ? 'none' : 'block';
        }
    })

    document.addEventListener('click', function (e) {
        const btnNavAdmin = document.querySelector('.btnNavAdmin');
        if (btnNavAdmin.contains(e.target)) {
            const menuAdmin = document.querySelector('.lienAdmin');
            menuAdmin.style.display = (menuAdmin.style.display === 'block') ? 'none' : 'block';
        }
    })

    // fermeture des dropdown
    document.addEventListener('click', function (e) {
        const menuUtilisateur = document.querySelector('.navUtilisateur');
        if (!menuUtilisateur.contains(e.target)) {
            document.querySelector('.lienUtilisateur').style.display = 'none';
        }
    })

    document.addEventListener('click', function (e) {
        const menuEmploye = document.querySelector('.navEmploye');
        if (!menuEmploye.contains(e.target)) {
            document.querySelector('.lienEmploye').style.display = 'none';
        }
    })

    document.addEventListener('click', function (e) {
        const menuAdmin = document.querySelector('.navAdmin');
        if (!menuAdmin.contains(e.target)) {
            document.querySelector('.lienAdmin').style.display = 'none';
        }
    })

});