document.addEventListener('DOMContentLoaded', () => {

    const avis = document.querySelectorAll('.divAvis');

    const btnSuivant = document.getElementById('btnSuivant');
    const btnPrecedent = document.getElementById('btnPrecedent');

    let index = 0;

    function afficherAvis(i) {

        avis.forEach(div => {
            div.style.opacity = '0';
            setTimeout(() => {
                div.style.display = 'none';
            }, 300);
        });

        setTimeout(() => {
            avis[i].style.display = 'block';
            setTimeout(() => {
                avis[i].style.opacity = '1';
            }, 10);
        }, 300);
    }

    afficherAvis(0);

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
            index = avis.length - 1;
        }

        afficherAvis(index);
    });
})