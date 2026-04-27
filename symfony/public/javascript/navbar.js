// DOMContentLoaded permet de charger le JS uniquement et strictement quand le HTML est prêt
document.addEventListener('DOMContentLoaded', function () {

    // Ouverture du menu déroulant
    document.querySelector('.btnNavEmploye').addEventListener('click', function () {
        const menu = document.querySelector('.lienEmploye');
        menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
    });

    // Ferme le menu déroulant en cas de clique sur l'interface
    document.addEventListener('click', function (e) {
        const fermer = document.querySelector('.navEmploye');

        if (!fermer.contains(e.target)) {
            document.querySelector('.lienEmploye').style.display = 'none';
        };
    });

});