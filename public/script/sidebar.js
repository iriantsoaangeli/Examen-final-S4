/**
 * Comportement de la sidebar :
 * - Sur PC : bouton pour la réduire (icônes seules), état mémorisé.
 * - Sur mobile/tablette : menu en tiroir (hors-champ), ouvert via le
 *   bouton "burger" et fermé via le fond sombre ou la touche Échap.
 */
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const collapseBtn = document.getElementById('sidebarCollapseBtn');
    const burger = document.getElementById('sidebarBurger');
    const backdrop = document.getElementById('sidebarBackdrop');

    if (!sidebar) {
        return;
    }

    const STORAGE_KEY = 'cash.sidebarCollapsed';

    // --- Réduction (desktop) ---
    if (localStorage.getItem(STORAGE_KEY) === 'true') {
        sidebar.classList.add('collapsed');
    }

    collapseBtn?.addEventListener('click', () => {
        const collapsed = sidebar.classList.toggle('collapsed');
        localStorage.setItem(STORAGE_KEY, collapsed ? 'true' : 'false');
    });

    // --- Menu en tiroir (mobile) ---
    function openMobileMenu() {
        sidebar.classList.add('mobile-open');
        backdrop?.classList.add('show');
        burger?.setAttribute('aria-expanded', 'true');
    }

    function closeMobileMenu() {
        sidebar.classList.remove('mobile-open');
        backdrop?.classList.remove('show');
        burger?.setAttribute('aria-expanded', 'false');
    }

    burger?.addEventListener('click', () => {
        if (sidebar.classList.contains('mobile-open')) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    });

    backdrop?.addEventListener('click', closeMobileMenu);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeMobileMenu();
        }
    });

    // Referme le tiroir si l'écran redevient large (rotation, redimensionnement).
    window.addEventListener('resize', () => {
        if (window.innerWidth > 992) {
            closeMobileMenu();
        }
    });
});
