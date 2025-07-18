/**
 * Protection contre la double soumission des formulaires
 * Ce script empêche les utilisateurs de soumettre plusieurs fois le même formulaire
 */

document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner tous les boutons submit avec l'ID submitBtn
    const submitButtons = document.querySelectorAll('button[type="submit"][id="submitBtn"]');

    submitButtons.forEach(button => {
        // Vérifier si le bouton n'a pas déjà un onclick
        if (!button.hasAttribute('onclick')) {
            button.addEventListener('click', function(e) {
                // Désactiver le bouton
                this.disabled = true;

                // Sauvegarder le texte original
                const originalText = this.innerHTML;

                // Afficher le spinner et le texte de chargement
                this.innerHTML = '<span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>Enregistrement en cours...';

                // Soumettre le formulaire après un court délai pour permettre l'animation
                setTimeout(() => {
                    this.form.submit();
                }, 100);

                // Empêcher la soumission multiple
                e.preventDefault();
            });
        }
    });

    // Protection supplémentaire pour tous les formulaires
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        let isSubmitting = false;

        form.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }

            isSubmitting = true;

            // Réactiver après 10 secondes au cas où
            setTimeout(() => {
                isSubmitting = false;
            }, 10000);
        });
    });
});

/**
 * Fonction utilitaire pour activer la protection sur un bouton spécifique
 * @param {HTMLElement} button - Le bouton à protéger
 * @param {string} loadingText - Le texte à afficher pendant le chargement
 */
function enableFormProtection(button, loadingText = 'Enregistrement en cours...') {
    if (!button) return;

    button.addEventListener('click', function(e) {
        if (this.disabled) {
            e.preventDefault();
            return false;
        }

        this.disabled = true;
        const originalText = this.innerHTML;

        this.innerHTML = `<span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>${loadingText}`;

        setTimeout(() => {
            this.form.submit();
        }, 100);

        e.preventDefault();
    });
}
