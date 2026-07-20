/**
 * Validation des numéros de téléphone (Madagascar).
 * Format attendu : 10 chiffres commençant par 0. Ex : 0341234567
 *
 * Préfixes valides (3 premiers chiffres) :
 *   - 032          -> Orange
 *   - 034 / 038    -> Telma
 */

const VALID_PREFIXES = ['032', '034', '038'];

/**
 * Vérifie que le numéro respecte le format : 10 chiffres, commence par 0.
 * @param {string} numero
 * @returns {boolean}
 */
function checkNum(numero) {
    return /^0[0-9]{9}$/.test(String(numero).trim());
}

/**
 * Vérifie qu'un préfixe (3 chiffres) correspond à un opérateur connu.
 * @param {string} prefix
 * @returns {boolean}
 */
function checkPrefix(prefix) {
    return VALID_PREFIXES.includes(String(prefix).trim());
}

/**
 * Extrait le préfixe (3 premiers chiffres) d'un numéro.
 * @param {string} numero
 * @returns {string}
 */
function getPrefix(numero) {
    return String(numero).trim().substring(0, 3);
}

/**
 * Validation complète : format + préfixe reconnu.
 * @param {string} numero
 * @returns {boolean}
 */
function isValidNumero(numero) {
    return checkNum(numero) && checkPrefix(getPrefix(numero));
}
