// DOMContentLoaded permet de charger le JS uniquement et strictement quand le HTML est prêt
document.addEventListener('DOMContentLoaded', () => {

    const prixMax = document.getElementById('prixMax');
    const theme = document.getElementById('theme');
    const regime = document.getElementById('regime');
    const nbPersonne = document.getElementById('nbPersonne');

    const menuList = document.getElementById('menuList');

    function fetchMenus() {

        // URLSearchParams transforme les valeurs en URL
        const params = new URLSearchParams({
            prixMax: prixMax.value,
            theme: theme.value,
            regime: regime.value,
            nbPersonne: nbPersonne.value
        });

        // Appel AJAX qui envoie une requête au contrôleur sans recharger la page
        fetch('/menu/filtre?' + params.toString())
            // Vue Twig complète renvoyé par le contrôleur
            .then(response => response.text())
            .then(html => {

                // Rend la Vue Twig manipulable
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Extraction ciblée
                const newMenuList = doc.querySelector('#menuList');

                // Mise à jour du DOM
                if (newMenuList) {
                    menuList.innerHTML = newMenuList.innerHTML;
                    menu = document.querySelectorAll('.carteMenu');
                    index = 0;
                    afficherMenu(index);
                }
            });
    }

    prixMax.addEventListener('input', fetchMenus);
    theme.addEventListener('change', fetchMenus);
    regime.addEventListener('change', fetchMenus);
    nbPersonne.addEventListener('input', fetchMenus);

    let menu = document.querySelectorAll('.carteMenu');

    const btnSuivant = document.getElementById('btnSuivant');
    const btnPrecedent = document.getElementById('btnPrecedent');

    let index = 0;


    // Détermine le nombre de menus affichés selon la largeur de l'écran
    function getNombreMenus() {

        if (window.innerWidth <= 768) {
            return 1;
        }

        if (window.innerWidth <= 1366) {
            return 2;
        }

        return 3;
    }


    function afficherMenu(i) {

        const nombreMenus = getNombreMenus();

        menu.forEach(div => {
            div.classList.remove('active');
        });

        for (let j = i; j < i + nombreMenus && j < menu.length; j++) {
            menu[j].classList.add('active');
        }
    }


    btnSuivant.addEventListener('click', () => {

        const nombreMenus = getNombreMenus();

        index += nombreMenus;

        if (index >= menu.length) {
            index = 0;
        }

        afficherMenu(index);
    });


    btnPrecedent.addEventListener('click', () => {

        const nombreMenus = getNombreMenus();

        index -= nombreMenus;

        if (index < 0) {
            index = Math.floor((menu.length - 1) / nombreMenus) * nombreMenus;
        }

        afficherMenu(index);
    });


    // Recalcule l'affichage lorsque la largeur de la fenêtre change
    window.addEventListener('resize', () => {
        index = 0;
        afficherMenu(index);
    });


    afficherMenu(index);

});