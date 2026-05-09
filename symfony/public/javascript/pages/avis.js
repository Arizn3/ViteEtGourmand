document.addEventListener('DOMContentLoaded', () => {

    const avis = document.querySelectorAll('.divAvis');

    const btnSuivant = document.getElementById('btnSuivant');
    const btnPrecedent = document.getElementById('btnPrecedent');

    let index = 0;

    function afficherAvis(i) {
        avis.forEach(div => {
            div.style.display = 'none';
        });

        avis[i].style.display = 'block'
    }

    btnSuivant.addEventListener('click', () => {
        index++;

        if (index >= avis.length) {
            index = 0;
        }

        afficherAvis(index);
    });

    btnPrecedent.addEventListener('click', () => {
        index--;

        if (index < 0) {
            index = avis.length -1;
        }

        afficherAvis(index);
    });
})