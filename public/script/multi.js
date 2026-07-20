/**
 * Fonctions de validation de numéro (Madagascar) adaptées de l'existant.
 */
let prefixCache = null;

async function fetchPrefixes() {
    if (prefixCache !== null) return prefixCache;
    try {
        const response = await fetch('/api/prefix', { headers: { 'Accept': 'application/json' } });
        if (!response.ok) throw new Error();
        prefixCache = await response.json();
        return prefixCache;
    } catch (e) {
        // Fallback incluant le nouvel opérateur 069 si l'API échoue momentanément
        return [
            { value: "032" }, { value: "037" }, 
            { value: "034" }, { value: "038" }, 
            { value: "033" }, { value: "039" }, 
            { value: "069" }
        ];
    }
}

function checkNum(numero) {
    return /^0[0-9]{9}$/.test(String(numero).trim());
}

async function getPrefix(numero) {
    const value = String(numero).trim().substring(0, 3);
    const prefixes = await fetchPrefixes();
    return prefixes.find((p) => p.value === value) ?? null;
}

async function isValidNumero(numero) {
    if (!checkNum(numero)) return false;
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

    // Fonction pour mettre à jour l'affichage globale du formulaire
    function updateFormState() {
        if (addedNumeros.size > 0) {
            fieldset.classList.remove('d-none');
            btnSubmit.removeAttribute('disabled');
        } else {
            fieldset.classList.add('d-none');
            btnSubmit.setAttribute('disabled', 'true');
        }
    }

    // Détection du clic sur le bouton (+)
    btnAddNumero.addEventListener('click', async () => {
        const numero = numeroInput.value.trim();
        numeroError.classList.add('d-none');

        if (addedNumeros.has(numero)) {
            numeroError.textContent = "Ce numéro a déjà été ajouté.";
            numeroError.classList.remove('d-none');
            return;
        }

        // Vérification Regex + Préfixe via l'API
        const valid = await isValidNumero(numero);
        if (!valid) {
            numeroError.textContent = "Numéro ou préfixe invalide (Format: 03xxxxxx ou 069xxxxxx).";
            numeroError.classList.remove('d-none');
            return;
        }

        // Ajout au Set
        addedNumeros.add(numero);

        // Création de l'élément DOM (Checkbox multiselect + Bouton (-))
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
        numeroInput.value = ''; // Reset de l'input
        updateFormState();
    });

    // Gérer l'ajout avec la touche Entrée
    numeroInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            btnAddNumero.click();
        }
    });

    // Délégation d'événement pour intercepter le bouton (-)
    container.addEventListener('click', (e) => {
        const btnRemove = e.target.closest('.btn-remove');
        if (btnRemove) {
            const numeroToRemove = btnRemove.getAttribute('data-numero');
            
            // Suppression du DOM et du Set
            const row = document.getElementById(`row_${numeroToRemove}`);
            if (row) row.remove();
            
            addedNumeros.delete(numeroToRemove);
            updateFormState();
        }
    });
});