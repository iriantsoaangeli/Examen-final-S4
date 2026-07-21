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

    const apiUrl = window.PREFIX_API_URL || '/api/prefix';
    const response = await fetch(apiUrl, {
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
