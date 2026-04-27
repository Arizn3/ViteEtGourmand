// La fonction anonyme s'exécute automatiquement, elle s'arrête si une balise <canvas> est définie en HTML,
// et régénère un graphique, ce processus évitent des bug engendré par une double lecture du fichier JS.
(function () {
    // Récupération du canvas HTML
    var canvas = document.getElementById('chart');

    if (!canvas) return;

    // Récupération des données passés depuis Twig
    var labels = JSON.parse(canvas.dataset.labels);
    var values = JSON.parse(canvas.dataset.values);

    // Sécuriter pour l'affichage du graphique, si un graphique est
    // déjà en cours d'utilisation, il est supprimé.
    if (window.chartInstance) {
        window.chartInstance.destroy();
    };

    // Création du graphique via Chart.js
    window.chartInstance = new Chart(canvas, {
        // Type du graphique
        type: 'pie',
        // Données affichées
        data: {
            labels: labels,
            datasets: [{ data: values }],
            // Options du graphiques
            options: {
                responsive: true,
                // Respect du CSS local 
                maintainAspectRatio: false,
                plugins: {
                    toolip: {
                        callbacks: {
                            label: function (context) {
                                return context.label + ' : ' + context.raw + ' commandes';
                            },
                        },
                    },
                },
            },
        },
    });
})();