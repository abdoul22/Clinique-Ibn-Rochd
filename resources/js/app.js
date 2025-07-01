// Fonction pour initialiser le dark mode immédiatement
function initializeDarkMode() {
    const isDark = localStorage.getItem('theme') === 'dark';
    if (isDark) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    // Mettre à jour l'icône et le label
    const icon = document.getElementById('darkmode-icon');
    const label = document.getElementById('darkmode-label');
    if (icon) icon.textContent = isDark ? '☀️' : '🌙';
    if (label) label.textContent = isDark ? 'Mode clair' : 'Mode sombre';
}

// Fonction pour appliquer les styles dark mode aux tableaux
function applyDarkModeToTables() {
    const tables = document.querySelectorAll('table');

    tables.forEach(table => {
        // Vérifier si le tableau a déjà les nouvelles classes
        if (table.classList.contains('table-main')) {
            return; // Déjà stylé
        }

        // Appliquer les styles dark mode de base
        table.classList.add('w-full', 'text-sm', 'text-left', 'text-gray-900', 'dark:text-gray-100');

        // Styliser l'en-tête
        const thead = table.querySelector('thead');
        if (thead) {
            thead.classList.add('bg-gray-50', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');

            // Styliser les cellules d'en-tête
            const thElements = thead.querySelectorAll('th');
            thElements.forEach(th => {
                th.classList.add('py-3', 'px-4', 'font-medium');
            });
        }

        // Styliser le corps
        const tbody = table.querySelector('tbody');
        if (tbody) {
            tbody.classList.add('divide-y', 'divide-gray-100', 'dark:divide-gray-600');

            // Styliser les lignes
            const rows = tbody.querySelectorAll('tr');
            rows.forEach(row => {
                row.classList.add('hover:bg-gray-50', 'dark:hover:bg-gray-700', 'transition-colors', 'duration-200');

                // Styliser les cellules
                const cells = row.querySelectorAll('td');
                cells.forEach(cell => {
                    cell.classList.add('py-4', 'px-4', 'text-gray-900', 'dark:text-gray-100');
                });
            });
        }
    });

    // Styliser les conteneurs de tableaux
    const tableContainers = document.querySelectorAll('.overflow-x-auto, .bg-white');
    tableContainers.forEach(container => {
        if (container.querySelector('table') && !container.classList.contains('table-container')) {
            container.classList.add('bg-white', 'dark:bg-gray-800', 'rounded-xl', 'shadow-sm', 'overflow-hidden', 'border', 'border-gray-200', 'dark:border-gray-700');
        }
    });
}

// Fonction globale pour le toggle dark mode
window.toggleDarkMode = function() {
    const html = document.documentElement;
    const isDark = html.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');

    const icon = document.getElementById('darkmode-icon');
    const label = document.getElementById('darkmode-label');

    if (icon) icon.textContent = isDark ? '☀️' : '🌙';
    if (label) label.textContent = isDark ? 'Mode clair' : 'Mode sombre';

    // Réappliquer les styles aux tableaux après le changement
    setTimeout(applyDarkModeToTables, 100);
};

// Initialiser immédiatement au chargement du script
initializeDarkMode();

// Attendre que le DOM soit prêt
document.addEventListener('DOMContentLoaded', function() {
    // Réinitialiser le dark mode (au cas où le DOM aurait changé)
    initializeDarkMode();

    // Appliquer les styles aux tableaux
    applyDarkModeToTables();

    // Observer les changements de classe dark sur html
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                // Réappliquer les styles après un changement de mode
                setTimeout(applyDarkModeToTables, 100);
            }
        });
    });

    // Observer les changements sur l'élément html
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
});

// Écouter les événements de navigation (pour les SPA ou les rechargements partiels)
window.addEventListener('popstate', function() {
    setTimeout(function() {
        initializeDarkMode();
        applyDarkModeToTables();
    }, 100);
});

// Écouter les changements de contenu (pour les applications dynamiques)
if (typeof window.MutationObserver !== 'undefined') {
    const contentObserver = new MutationObserver(function(mutations) {
        let shouldReapply = false;
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                // Vérifier si de nouveaux tableaux ont été ajoutés
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && (node.tagName === 'TABLE' || node.querySelector('table'))) {
                        shouldReapply = true;
                    }
                });
            }
        });

        if (shouldReapply) {
            setTimeout(applyDarkModeToTables, 100);
        }
    });

    // Observer les changements dans le body
    contentObserver.observe(document.body, {
        childList: true,
        subtree: true
    });
}
