/**
 * Validation des numéros de téléphone (Madagascar).
 * Format attendu : 10 chiffres commençant par 0. Ex : 0341234567
 *
 * Les préfixes valides (3 premiers chiffres) et leur opérateur ne sont
 * plus codés en dur ici : ils sont récupérés en base via l'API
 * GET /api/prefix (voir APIController::getPrefix).
 */

let prefixCache = null;

/**
 * Récupère (et met en cache) la liste des préfixes connus en base,
 * sous la forme [{ value: "032", operator_id: 1, operator: "Orange" }, ...].
 * @returns {Promise<Array>}
 */
async function fetchPrefixes() {
    if (prefixCache !== null) {
        return prefixCache;
    }

    const response = await fetch('/api/prefix', {
        headers: { 'Accept': 'application/json' },
    });

    if (!response.ok) {
        throw new Error('Impossible de récupérer les préfixes depuis le serveur.');
    }

    prefixCache = await response.json();
    return prefixCache;
}

/**
 * Vérifie que le numéro respecte le format : 10 chiffres, commence par 0.
 * Purement syntaxique, ne fait pas d'appel réseau.
 * @param {string} numero
 * @returns {boolean}
 */
function checkNum(numero) {
    return /^0[0-9]{9}$/.test(String(numero).trim());
}

/**
 * Retrouve, en base (via /api/prefix), le préfixe et l'opérateur
 * correspondant aux 3 premiers chiffres du numéro.
 * @param {string} numero
 * @returns {Promise<{value: string, operator_id: number, operator: string}|null>}
 */
async function getPrefix(numero) {
    const value = String(numero).trim().substring(0, 3);
    const prefixes = await fetchPrefixes();

    return prefixes.find((p) => p.value === value) ?? null;
}

/**
 * Vérifie qu'un préfixe (3 chiffres) correspond à un opérateur connu en base.
 * @param {string} prefix
 * @returns {Promise<boolean>}
 */
async function checkPrefix(prefix) {
    const prefixes = await fetchPrefixes();
    return prefixes.some((p) => p.value === String(prefix).trim());
}

/**
 * Validation complète : format + préfixe reconnu en base.
 * @param {string} numero
 * @returns {Promise<boolean>}
 */
async function isValidNumero(numero) {
    if (!checkNum(numero)) {
        return false;
    }

    return (await getPrefix(numero)) !== null;
}

// Ensemble pour stocker les numéros uniques ajoutés
const addedNumeros = new Set();

document.addEventListener('DOMContentLoaded', () => {
    const numeroInput = document.getElementById('numero_input');
    const btnAddNumero = document.getElementById('btn_add_numero');
    const numeroError = document.getElementById('numero_error');
    const fieldset = document.getElementById('destinataires_fieldset');
    const container = document.getElementById('destinataires_container');
    const btnSubmit = document.getElementById('btn_submit');

    // Met à jour l'affichage global du formulaire (affichage du fieldset et activation du bouton de validation)
    function updateFormState() {
        if (addedNumeros.size > 0) {
            fieldset.classList.remove('d-none');
            btnSubmit.removeAttribute('disabled');
        } else {
            fieldset.classList.add('d-none');
            btnSubmit.setAttribute('disabled', 'true');
        }
    }

    // Détection du clic sur le bouton (+) pour ajouter un numéro
    btnAddNumero.addEventListener('click', async () => {
        const numero = numeroInput.value.trim();
        numeroError.classList.add('d-none');

        if (addedNumeros.has(numero)) {
            numeroError.textContent = "Ce numéro a déjà été ajouté.";
            numeroError.classList.remove('d-none');
            return;
        }

        // Utilisation de la validation asynchrone demandée
        try {
            const valid = await isValidNumero(numero);
            if (!valid) {
                numeroError.textContent = "Numéro ou préfixe invalide (Format: 10 chiffres commençant par 0 avec préfixe valide).";
                numeroError.classList.remove('d-none');
                return;
            }
        } catch (error) {
            numeroError.textContent = "Erreur lors de la vérification du numéro.";
            numeroError.classList.remove('d-none');
            return;
        }

        // Enregistrement du numéro validé
        addedNumeros.add(numero);

        // Création de la ligne DOM dynamique avec Checkbox et Bouton (-)
        const divRow = document.createElement('div');
        divRow.className = "d-flex align-items-center justify-content-between p-2 border-bottom";
        divRow.id = `row_${numero}`;

        divRow.innerHTML = `
            <div class="form-check">
                <input class="form-check-input row-checkbox" type="checkbox" name="numero_receiver[]" value="${numero}" id="chk_${numero}" checked>
                <label class="form-check-label fw-medium font-monospace text-dark" for="chk_${numero}">
                    ${numero}
                </label>
            </div>
            <button class="btn btn-sm btn-outline-danger btn-remove" type="button" data-numero="${numero}">
                <i class="bi bi-dash-lg"></i>
            </button>
        `;

        container.appendChild(divRow);
        numeroInput.value = ''; // Réinitialisation du champ
        updateFormState();
    });

    // Permet d'ajouter un numéro en pressant la touche 'Entrée'
    numeroInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            btnAddNumero.click();
        }
    });

    // Interception des clics pour le bouton (-) de suppression via délégation d'événement
    container.addEventListener('click', (e) => {
        const btnRemove = e.target.closest('.btn-remove');
        if (btnRemove) {
            const numeroToRemove = btnRemove.getAttribute('data-numero');
            
            // Retrait du DOM
            const row = document.getElementById(`row_${numeroToRemove}`);
            if (row) {
                row.remove();
            }
            
            // Retrait de la structure de données
            addedNumeros.delete(numeroToRemove);
            updateFormState();
        }
    });
});