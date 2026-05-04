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

                // Extraction ciblé, sans ça toute la Vue Twig est renvoyé
                const newMenuList = doc.querySelector('#menuList');

                // Mise à jour du DOM
                if (newMenuList) {
                    menuList.innerHTML = newMenuList.innerHTML;
                }
            });
    };

    prixMax.addEventListener('input', fetchMenus);
    theme.addEventListener('change', fetchMenus);
    regime.addEventListener('change', fetchMenus);
    nbPersonne.addEventListener('input', fetchMenus);

});