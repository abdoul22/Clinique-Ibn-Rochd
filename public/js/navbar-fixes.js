// Script pour corriger les bugs de navigation et du dark mode
document.addEventListener("DOMContentLoaded", function () {
    // 1. Fermer le menu déroulant lors de la navigation
    function closeDropdowns() {
        // Fermer tous les dropdowns Alpine.js
        const dropdowns = document.querySelectorAll('[x-data*="open"]');
        dropdowns.forEach((dropdown) => {
            if (
                dropdown._x_dataStack &&
                dropdown._x_dataStack[0] &&
                dropdown._x_dataStack[0].open
            ) {
                dropdown._x_dataStack[0].open = false;
            }
        });

        // Fermer les menus déroulants classiques
        const mobileMenus = document.querySelectorAll(
            '[x-data*="mobileMenuOpen"]'
        );
        mobileMenus.forEach((menu) => {
            if (
                menu._x_dataStack &&
                menu._x_dataStack[0] &&
                menu._x_dataStack[0].mobileMenuOpen
            ) {
                menu._x_dataStack[0].mobileMenuOpen = false;
            }
        });
    }

    // 2. Corriger l'affichage du dark mode au chargement
    function fixDarkModeDisplay() {
        const darkModeButton = document.querySelector('[x-data*="dark"]');
        if (darkModeButton) {
            // Attendre qu'Alpine.js soit initialisé
            setTimeout(() => {
                const isDark =
                    localStorage.getItem("theme") === "dark" ||
                    (!("theme" in localStorage) &&
                        window.matchMedia("(prefers-color-scheme: dark)")
                            .matches);

                // Forcer la mise à jour de l'affichage
                if (
                    darkModeButton._x_dataStack &&
                    darkModeButton._x_dataStack[0]
                ) {
                    darkModeButton._x_dataStack[0].dark = isDark;
                }

                // S'assurer que les icônes sont correctement affichées
                const sunIcon = darkModeButton.querySelector(
                    'svg[x-show="!dark"]'
                );
                const moonIcon =
                    darkModeButton.querySelector('svg[x-show="dark"]');

                if (sunIcon && moonIcon) {
                    if (isDark) {
                        sunIcon.style.display = "none";
                        moonIcon.style.display = "block";
                    } else {
                        sunIcon.style.display = "block";
                        moonIcon.style.display = "none";
                    }
                }
            }, 100);
        }
    }

    // 3. Écouter les changements de page
    function handlePageNavigation() {
        // Fermer les dropdowns avant la navigation
        closeDropdowns();

        // Corriger l'affichage du dark mode
        setTimeout(fixDarkModeDisplay, 50);
    }

    // 4. Écouter les clics sur les liens de navigation
    document.addEventListener("click", function (e) {
        const link = e.target.closest("a");
        if (link && link.href && !link.href.includes("#")) {
            // Fermer les dropdowns avant la navigation
            closeDropdowns();
        }
    });

    // 5. Écouter les soumissions de formulaire
    document.addEventListener("submit", function (e) {
        closeDropdowns();
    });

    // 6. Corriger l'affichage initial du dark mode
    fixDarkModeDisplay();

    // 7. Corriger l'affichage après le chargement complet
    window.addEventListener("load", function () {
        fixDarkModeDisplay();
    });

    // 8. Écouter les changements de localStorage pour le dark mode
    window.addEventListener("storage", function (e) {
        if (e.key === "theme") {
            setTimeout(fixDarkModeDisplay, 50);
        }
    });

    // 9. Corriger l'affichage lors des changements de dark mode
    document.addEventListener("darkModeChanged", function () {
        setTimeout(fixDarkModeDisplay, 50);
    });

    // 10. Prévenir l'affichage simultané des icônes
    function preventIconOverlap() {
        const darkModeButton = document.querySelector('[x-data*="dark"]');
        if (darkModeButton) {
            const sunIcon = darkModeButton.querySelector('svg[x-show="!dark"]');
            const moonIcon = darkModeButton.querySelector('svg[x-show="dark"]');

            if (sunIcon && moonIcon) {
                // S'assurer qu'une seule icône est visible à la fois
                const isDark =
                    document.documentElement.classList.contains("dark");

                if (isDark) {
                    sunIcon.style.display = "none";
                    moonIcon.style.display = "block";
                } else {
                    sunIcon.style.display = "block";
                    moonIcon.style.display = "none";
                }
            }
        }
    }

    // Exécuter la prévention d'overlap
    preventIconOverlap();

    // Réexécuter après un délai pour s'assurer que tout est chargé
    setTimeout(preventIconOverlap, 200);
});

// Script pour corriger l'affichage du dark mode lors de la première visite
window.addEventListener("beforeunload", function () {
    // Sauvegarder l'état du dark mode
    const isDark = document.documentElement.classList.contains("dark");
    if (!localStorage.getItem("theme")) {
        localStorage.setItem("theme", isDark ? "dark" : "light");
    }
});

// Script pour s'assurer que le dark mode est correctement initialisé
document.addEventListener("alpine:init", function () {
    // Attendre qu'Alpine.js soit complètement initialisé
    setTimeout(function () {
        const darkModeButton = document.querySelector('[x-data*="dark"]');
        if (darkModeButton) {
            const isDark =
                localStorage.getItem("theme") === "dark" ||
                (!("theme" in localStorage) &&
                    window.matchMedia("(prefers-color-scheme: dark)").matches);

            // Forcer la mise à jour de l'état
            if (darkModeButton._x_dataStack && darkModeButton._x_dataStack[0]) {
                darkModeButton._x_dataStack[0].dark = isDark;
            }

            // Corriger l'affichage des icônes
            const sunIcon = darkModeButton.querySelector('svg[x-show="!dark"]');
            const moonIcon = darkModeButton.querySelector('svg[x-show="dark"]');

            if (sunIcon && moonIcon) {
                if (isDark) {
                    sunIcon.style.display = "none";
                    moonIcon.style.display = "block";
                } else {
                    sunIcon.style.display = "block";
                    moonIcon.style.display = "none";
                }
            }
        }
    }, 100);
});
