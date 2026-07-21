<?php

/**
 * =============================================================================
 *  reference.php — CARTE DU PROJET "CASH" (Mobile Money - CodeIgniter 4)
 * =============================================================================
 *
 * A QUOI SERT CE FICHIER ?
 * -------------------------------------------------------------------------
 * Ce fichier n'est JAMAIS exécuté par l'application (aucune route ne pointe
 * dessus). C'est un plan/index de navigation : pour chaque fonctionnalité,
 * on liste ses sous-fonctionnalités et, pour chacune, les 3 endroits où
 * on peut avoir besoin d'aller modifier du code :
 *
 *      VUE        -> l'affichage HTML (app/Views/...)
 *      CONTROLLER -> la logique de requête / orchestration
 *      MODEL      -> l'accès aux données (requêtes SQL)
 *
 * COMMENT L'UTILISER ?
 * -------------------------------------------------------------------------
 * Les blocs `include("...");` tout en haut sont réels : Ctrl+Click (ou
 * Cmd+Click) sur le chemin ouvre directement le fichier PHP inclus.
 * Les tableaux `[NomDeClasse::class, 'nomMethode']` restent cliquables
 * (PhpStorm / VSCode+Intelephense résolvent les "array callables") :
 * Ctrl+Click sur le nom de la méthode saute dedans.
 * Les vues et fichiers JS/CSS n'étant pas des classes PHP, ils sont
 * référencés par leur chemin en commentaire `@see chemin/vers/fichier`.
 *
 * QUESTION A SE POSER POUR CHAQUE MODIFICATION :
 * "Je veux changer CETTE sortie, je touche quoi ?"
 *   - L'apparence / le texte / le HTML          -> la VUE
 *   - Le comportement au clic / la redirection  -> le CONTROLLER
 *   - Le calcul, la validation, la donnée brute -> le MODEL (ou le Controller
 *     si c'est une règle métier de calcul comme les frais/commissions)
 *   - Le clavier virtuel front / validation live -> le JS (public/script/*)
 *   - Les couleurs / espacements / cartes        -> le CSS (public/css/custom.css)
 * =============================================================================
 */

// ---------------------------------------------------------------------------
// IMPORTS — Ctrl+Click sur n'importe quelle classe ci-dessous ouvre son fichier
// ---------------------------------------------------------------------------
include("app/Config/Routes.php");   // @see app/Config/Routes.php   -> déclaration de toutes les URLs
include("app/Config/Filters.php");  // @see app/Config/Filters.php  -> quel filtre s'applique à quelle route
include("app/Controllers/AuthController.php");
include("app/Controllers/Home.php");
include("app/Controllers/APIController.php");
include("app/Controllers/operations/Operation.php");
include("app/Controllers/rapports/Commission.php");

include("app/Models/UserModel.php");
include("app/Models/MvtModel.php");
include("app/Models/MvtDetailsModel.php");
include("app/Models/PrefixModel.php");
include("app/Models/TrancheModel.php");
include("app/Models/TypeMvtModel.php");
include("app/Models/CommissionModel.php");

include("app/Filters/AuthFilter.php");
include("app/Filters/OPFilter.php");

class AuthController extends \App\Controllers\AuthController {}
class Home extends \App\Controllers\Home {}
class APIController extends \App\Controllers\APIController {}
class Operation extends \App\Controllers\operations\Operation {}
class Commission extends \App\Controllers\rapports\Commission {}

class UserModel extends \App\Models\UserModel {}
class MvtModel extends \App\Models\MvtModel {}
class MvtDetailsModel extends \App\Models\MvtDetailsModel {}
class PrefixModel extends \App\Models\PrefixModel {}
class TrancheModel extends \App\Models\TrancheModel {}
class TypeMvtModel extends \App\Models\TypeMvtModel {}
class CommissionModel extends \App\Models\CommissionModel {}

class AuthFilter extends \App\Filters\AuthFilter {}
class OPFilter extends \App\Filters\OPFilter {}


/* =============================================================================
 * 0. BASE DE DONNEES — app/Database (SQLite en dev) + racine base.sql
 * =============================================================================
 * @see base.sql  -> structure complète des tables + données de référence
 *
 * Tables et leur rôle :
 *   - operator       : liste des opérateurs mobiles (Orange, Telma, Airtel, Blueline, Opérateur 069)
 *   - prefix         : préfixe téléphonique (ex: '032') -> operator_id
 *   - type_mvt       : les 3 types de mouvement (depot, retrait, transfert)
 *   - tranche        : barème de frais (inf/sup/frais) par type de mouvement
 *   - user           : numero(PK), nom, solde, is_provider (1 = fournisseur invisible)
 *   - mvt            : un mouvement d'argent (montant, frais, sender, receiver, instant...)
 *   - mvt_details    : lien mvt <-> tranche utilisée pour ce mouvement
 *   - comission      : taux de commission inter-opérateurs (id_op1, id_op2, commission_rate)
 *   - mvt_commission : commission effectivement générée par un mvt inter-opérateur
 *
 * Pour changer un préfixe/opérateur ou un barème de frais -> éditer base.sql
 * (ou passer par les Models PrefixModel / TrancheModel si en prod).
 */


/* =============================================================================
 * 1. AUTHENTIFICATION (login sans mot de passe, juste le numéro)
 * =============================================================================
 * Route(s)   : GET  /login   -> AuthController::login
 *              POST /auth    -> AuthController::authenticate
 *              GET  /logout  -> AuthController::logout
 *              GET  /        -> AuthController::login
 * @see app/Config/Routes.php (lignes 10-13)
 */
const FONCTIONNALITE_1_AUTHENTIFICATION = [

    '1.1 Affichage du formulaire de login' => [
        'vue' => include("app/Views/login/login.php"),          // @see app/Views/login/login.php
        'controller' => [AuthController::class, 'login'],
        'note' => 'Aucun model : la vue est juste rendue telle quelle.',
    ],

    '1.2 Validation du format du numéro (10 chiffres, commence par 0)' => [
        'controller' => [AuthController::class, 'regex'],
        'regle' => 'preg_match(/^0[0-9]{9}$/) puis vérif du préfixe en base',
        'model' => [PrefixModel::class, 'isValidNumero'],
    ],

    '1.3 Validation "live" côté navigateur (avant envoi du formulaire)' => [
        'js' => include("public/script/regex.js"),               // @see public/script/regex.js
        'fonctions_js' => ['checkNum()', 'getPrefix()', 'checkPrefix()', 'isValidNumero()'],
        'appelle' => 'GET /api/prefix (voir FONCTIONNALITE_9_API)',
    ],

    '1.4 Création automatique du compte si le numéro est inconnu' => [
        'controller' => [AuthController::class, 'authenticate'],
        'model' => [UserModel::class, 'exists'],
        'model_2' => [UserModel::class, 'createUser'],
    ],

    '1.5 Ouverture de session (numero, isLoggedIn, isOP, nom)' => [
        'controller' => [AuthController::class, 'authenticate'],
        'model' => [UserModel::class, 'findByNumero'],
        'note' => 'session()->set(...) est fait directement dans le controller.',
        'TODO_NON_FAIT' => "Taches.md ligne 'modifier log in pour inclure si l'user est un operateur' est cochée [ ] non faite. isOP est déjà set ici via is_provider, à vérifier si c'est suffisant.",
    ],

    '1.6 Déconnexion' => [
        'controller' => [AuthController::class, 'logout'],
        'action' => 'session()->destroy() puis redirection vers /login',
    ],

    '1.7 Protection des pages : obligation d\'être connecté' => [
        'filtre' => AuthFilter::class,                   // @see app/Filters/AuthFilter.php
        'methode' => [AuthFilter::class, 'before'],
        'branchement' => 'app/Config/Filters.php -> $globals["before"]["auth"]',
        'exceptions' => "login, auth, auth/*, api/*, register, assets/*",
    ],
];


/* =============================================================================
 * 2. LANDING PAGE / ACCUEIL (tableau de bord après connexion)
 * =============================================================================
 * Route(s) : GET /dashboard, GET /dashboard.html -> Home::dashboard
 * @see app/Config/Routes.php (lignes 15-16)
 */
const FONCTIONNALITE_2_DASHBOARD = [

    '2.1 Affichage du solde et des infos du compte' => [
        'vue' => include("app/Views/home/dashboard.php"),          // @see app/Views/home/dashboard.php
        'controller' => [Home::class, 'dashboard'],
        'model' => [UserModel::class, 'findByNumero'],
    ],

    '2.2 Liste des 3 dernières transactions (aperçu rapide)' => [
        'controller' => [Home::class, 'dashboard'],
        'model' => [MvtModel::class, 'getRecentForUser'],
        'note' => 'Le mapping "libellé humain" (Dépôt envoyé/reçu, etc.) est calculé
                    dans Home::dashboard() via une closure, PAS dans la vue ni le model.',
    ],

    '2.3 Liens de navigation vers les actions (sidebar)' => [
        'vue' => include("app/Views/partials/sidebar.php"),        // @see app/Views/partials/sidebar.php
        'note' => 'Inclus par $this->include(\'partials/sidebar\') dans presque toutes les vues.
                    Pour ajouter/retirer un lien du menu -> éditer $navItems ici.',
    ],
];


/* =============================================================================
 * 3. DEPOT
 * =============================================================================
 * Route(s) : GET+POST /operations/depot -> Operation::depot
 * @see app/Config/Routes.php (lignes 22-23)
 */
const FONCTIONNALITE_3_DEPOT = [

    '3.1 Affichage du formulaire de dépôt (GET)' => [
        'vue' => include("app/Views/operations/depot.php"),         // @see app/Views/operations/depot.php
        'controller' => [Operation::class, 'depot'],
        'controller_helper' => [Operation::class, 'renderOperationPage'],
    ],

    '3.2 Validation du numéro client bénéficiaire' => [
        'controller' => [Operation::class, 'resoudreParticipants'],
        'controller_helper' => [Operation::class, 'findClientByNumero'],
        'model' => [PrefixModel::class, 'isValidNumero'],
        'note' => 'Pour un dépôt, "sender" = le fournisseur invisible automatiquement
                    (voir getProviderNumero / getProviderByNumero).',
    ],

    '3.3 Calcul du montant et des frais (frais = 0 pour un dépôt)' => [
        'controller' => [Operation::class, 'calculerDebit'],
        'model' => [TrancheModel::class, 'findByTypeAndMontant'],
        'note' => "Barème de frais réel en base : tranche liée à type_mvt='depot'.",
    ],

    '3.4 Enregistrement du mouvement + mise à jour des soldes' => [
        'controller' => [Operation::class, 'enregistrerMouvement'],
        'controller_helper' => [Operation::class, 'incrementerSolde'],
        'model' => [MvtModel::class, 'insert'], // méthode héritée de CodeIgniter\Model
        'model_2' => [MvtDetailsModel::class, 'insert'],
        'transaction_sql' => 'this->mvtModel->db->transStart() / transComplete()',
    ],

    '3.5 Redirection vers le reçu' => [
        'controller' => [Operation::class, 'executerOperation'],
        'controller_helper' => [Operation::class, 'buildReceiptData'],
        'suite' => 'voir FONCTIONNALITE_7_RECU',
    ],
];


/* =============================================================================
 * 4. RETRAIT
 * =============================================================================
 * Route(s) : GET+POST /operations/retrait -> Operation::retrait
 * @see app/Config/Routes.php (lignes 24-25)
 * Le flux est identique au dépôt (voir FONCTIONNALITE_3), seules les
 * différences propres au retrait sont listées ici.
 */
const FONCTIONNALITE_4_RETRAIT = [

    '4.1 Affichage du formulaire de retrait (GET)' => [
        'vue' => include("app/Views/operations/retrait.php"),       // @see app/Views/operations/retrait.php
        'controller' => [Operation::class, 'retrait'],
    ],

    '4.2 Le "receiver" est automatiquement le fournisseur invisible' => [
        'controller' => [Operation::class, 'resoudreParticipants'],
        'model' => [UserModel::class, 'getProviderNumero'],
    ],

    '4.3 Validation du solde suffisant (montant + frais)' => [
        'controller' => [Operation::class, 'verifierSolde'],
        'regle' => 'if ((float) $sender[\'solde\'] < $debit) throw RuntimeException(\'Solde insuffisant.\')',
    ],

    '4.4 Calcul des frais de retrait (barème tranche)' => [
        'controller' => [Operation::class, 'calculerDebit'],
        'model' => [TrancheModel::class, 'findByTypeAndMontant'],
        'donnee_base' => "table tranche où id_type = id de 'retrait' (voir base.sql)",
    ],

    '4.5 Particularité comptable : pas de crédit au receiver' => [
        'controller' => [Operation::class, 'enregistrerMouvement'],
        'ligne' => 'if ($operation["type"] !== "retrait") { incrementerSolde(receiver...) }',
    ],
];


/* =============================================================================
 * 5. TRANSFERT (simple, 1 destinataire) + TRANSFERT MULTIPLE
 * =============================================================================
 * Route(s) simple  : GET+POST /operations/transfert          -> Operation::transfert
 *                     (groupe protégé par le filtre 'opfilter', voir 5.6)
 * Route(s) multiple : GET+POST /operations/transfert-multiple -> Operation::transfertMultiple
 * @see app/Config/Routes.php (lignes 27-38)
 */
const FONCTIONNALITE_5_TRANSFERT = [

    '5.1 Affichage du formulaire de transfert simple (GET)' => [
        'vue' => include("app/Views/operations/transfert.php"),      // @see app/Views/operations/transfert.php
        'controller' => [Operation::class, 'transfert'],
    ],

    '5.2 Choix du numéro destinataire existant + validation' => [
        'controller' => [Operation::class, 'resoudreParticipants'],
        'controller_helper' => [Operation::class, 'findClientByNumero'],
        'model' => [UserModel::class, 'findByNumero'],
        'model_2' => [PrefixModel::class, 'isValidNumero'],
    ],

    '5.3 Validation du solde suffisant' => [
        'controller' => [Operation::class, 'verifierSolde'],
    ],

    '5.4 Calcul des frais de transfert (barème tranche)' => [
        'controller' => [Operation::class, 'resoudreTranche'],
        'model' => [TrancheModel::class, 'findByTypeAndMontant'],
    ],

    '5.5 Calcul de la commission inter-opérateurs (si opérateurs différents)' => [
        'controller' => [Operation::class, 'calculerCommissionInterOperateur'],
        'controller_helper' => [Operation::class, 'getOperatorIdByPrefix'],
        'controller_helper_2' => [Operation::class, 'getCommissionRate'],
        'model' => [CommissionModel::class, 'findRate'],
        'regle' => 'Si substr(sender,0,3) === substr(receiver,0,3) -> commission = 0
                     (même opérateur). Sinon -> montant * comission.commission_rate.',
        'stockage' => [Operation::class, 'enregistrerCommissionInterOperateur'],
        'model_stockage' => [CommissionModel::class, 'enregistrerCommission'],
    ],

    '5.6 Filtre "opfilter" sur le groupe operations (transfert / historique / situation)' => [
        'filtre' => OPFilter::class,                       // @see app/Filters/OPFilter.php
        'methode' => [OPFilter::class, 'before'],
        'branchement' => "app/Config/Routes.php -> \$routes->group('operations', ['filter' => 'opfilter'], ...)",
        'comportement_actuel' => "if (!session()->get('isOP')) redirect vers operations/transfert-multiple",
        'ATTENTION' => 'Comportement à valider : ce filtre redirige les NON-opérateurs vers
                         transfert-multiple plutôt que de les bloquer. Nom/logique à
                         reclarifier si le besoin métier change (voir Taches.md :
                         "Empecher l\'acces a la deuxieme page d\'envoi").',
    ],

    '--- TRANSFERT MULTIPLE ---' => '=========================================',

    '5.7 Affichage du formulaire de transfert multiple (GET)' => [
        'vue' => include("app/Views/operations/transfert-multiple.php"), // @see app/Views/operations/transfert-multiple.php
        'controller' => [Operation::class, 'transfertMultiple'],
        'js' => include("public/script/multi.js"),                  // @see public/script/multi.js
        'js_role' => 'Ajout dynamique des numéros destinataires (bouton +, validation
                       live via isValidNumero(), suppression via bouton -).',
    ],

    '5.8 Préparation : split du montant global / nombre de destinataires' => [
        'controller' => [Operation::class, 'preparerTransfertMultiple'],
        'formule' => 'montantUnitaire = montantGlobal / nombreDestinataires',
        'model' => [TrancheModel::class, 'findByTypeAndMontant'],
    ],

    '5.9 Vérification globale du solde (débit total = montant + frais * nb destinataires)' => [
        'controller' => [Operation::class, 'preparerTransfertMultiple'],
    ],

    '5.10 Exécution : boucle sur chaque destinataire (transaction SQL globale)' => [
        'controller' => [Operation::class, 'executerTransfertMultiple'],
        'controller_helper' => [Operation::class, 'traiterDestinataireTransfertMultiple'],
        'model' => [MvtModel::class, 'insert'],
        'model_2' => [MvtDetailsModel::class, 'insert'],
        'commission_par_destinataire' => [Operation::class, 'enregistrerCommissionInterOperateur'],
    ],

    '5.11 Construction du reçu récapitulatif (envoi multiple)' => [
        'controller' => [Operation::class, 'buildTransfertMultipleReceipt'],
        'suite' => 'voir FONCTIONNALITE_7_RECU',
    ],
];


/* =============================================================================
 * 6. HISTORIQUE DES TRANSACTIONS
 * =============================================================================
 * Route(s) : GET /transactions, /transactions.html -> Home::transactions
 *            GET /operations/historique/(:segment)  -> Operation::historique (JSON, protégé opfilter)
 * @see app/Config/Routes.php (lignes 17-18, 32)
 */
const FONCTIONNALITE_6_HISTORIQUE = [

    '6.1 Page "Historique des transactions" (vue HTML, paginée)' => [
        'vue' => include("app/Views/history/historique.php"),        // @see app/Views/history/historique.php
        'controller' => [Home::class, 'transactions'],
        'model' => [MvtModel::class, 'getPaginatedForUser'],
    ],

    '6.2 Groupement par date + libellé humain (Aujourd\'hui / Hier / date)' => [
        'controller' => [Home::class, 'transactions'],
        'controller_helper' => [Home::class, 'moisFr'],
        'note' => 'Toute la logique de présentation (icônes, classes CSS merchant-*,
                    libellés "Dépôt envoyé/reçu"...) est calculée ici, PAS dans la vue.',
    ],

    '6.3 Pagination (widget de pages)' => [
        'vue' => include("app/Views/pager/ledger_pagination.php"),   // @see app/Views/pager/ledger_pagination.php
        'model' => [MvtModel::class, 'getPaginatedForUser'],
        'note' => 'Utilise le Pager natif CodeIgniter (this->pager apres ->paginate()).',
    ],

    '6.4 Endpoint JSON historique par client (filtré par numéro)' => [
        'route' => "GET /operations/historique/(:segment)",
        'controller' => [Operation::class, 'historique'],
        'model' => [MvtModel::class, 'getMouvementsByUser'],
        'protection' => 'Filtre opfilter (voir 5.6)',
    ],
];


/* =============================================================================
 * 7. RECU (page de confirmation après une opération)
 * =============================================================================
 * Route(s) : GET /recu, /receipt, /receipt.html -> Operation::recu
 * @see app/Config/Routes.php (ligne 20)
 */
const FONCTIONNALITE_7_RECU = [

    '7.1 Affichage du reçu (flashdata de session, une seule fois)' => [
        'vue' => include("app/Views/operations/recu.php"),           // @see app/Views/operations/recu.php
        'controller' => [Operation::class, 'recu'],
        'source_donnee' => "session()->getFlashdata('receipt')",
        'note' => "Le reçu n'est PAS stocké en base, c'est une flashdata : si on
                    rafraîchit la page, le message 'Aucun reçu récent' apparaît.",
    ],

    '7.2 Construction des données du reçu (dépôt/retrait/transfert simple)' => [
        'controller' => [Operation::class, 'buildReceiptData'],
    ],

    '7.3 Construction des données du reçu (transfert multiple)' => [
        'controller' => [Operation::class, 'buildTransfertMultipleReceipt'],
        'difference' => "type = 'transfert_multiple', receiver = liste des numéros séparés par virgule",
    ],
];


/* =============================================================================
 * 8. SITUATION OPERATEUR / RAPPORTS (menu sidebar "Rapports", classe Commission)
 * =============================================================================
 * Route(s) : GET /rapports            -> Commission::index
 *            GET /rapports/bareme     -> Commission::bareme
 *            GET /rapports/operateurs -> Commission::operateurs
 *            GET /rapports/comptes    -> Commission::comptes (JSON)
 * @see app/Config/Routes.php (lignes 40-43)
 * @see app/Controllers/rapports/Commission.php
 * (Anciennement nommé "Rapport"/"RapportModel", renommé en "Commission" —
 *  voir Taches.md section "Renommer Rapport en Commission")
 */
const FONCTIONNALITE_8_RAPPORTS = [

    '8.1 Rapport principal : commissions par opérateur + montants à envoyer' => [
        'vue' => include("app/Views/rapports/commission.php"),       // @see app/Views/rapports/commission.php
        'controller' => [Commission::class, 'index'],
        'model_a' => [CommissionModel::class, 'getCommissionsParOperateur'],
        'model_b' => [CommissionModel::class, 'getMontantsAEnvoyerParOperateur'],
        'note_menu' => 'Le lien sidebar garde le libellé "Rapports" même si la classe
                         s\'appelle Commission (voir app/Views/partials/sidebar.php).',
    ],

    '8.2 Barème des commissions configurées (table comission)' => [
        'vue' => include("app/Views/rapports/bareme.php"),           // @see app/Views/rapports/bareme.php
        'controller' => [Commission::class, 'bareme'],
        'model' => [CommissionModel::class, 'getConfigurationCommissions'],
    ],

    '8.3 Liste des opérateurs avec leur compte fournisseur (is_provider)' => [
        'vue' => include("app/Views/rapports/operateurs.php"),       // @see app/Views/rapports/operateurs.php
        'controller' => [Commission::class, 'operateurs'],
        'model' => [PrefixModel::class, 'operatorsWithProviders'],
    ],

    '8.4 Situation des comptes clients (liste numéro/nom/solde, JSON)' => [
        'controller' => [Commission::class, 'comptes'],
        'model' => [UserModel::class, 'getClients'],
        'regle' => 'Exclut le fournisseur invisible (is_provider = 0 uniquement).',
        'ATTENTION_DOUBLON' => "app/Config/Routes.php déclare AUSSI
            'situation/comptes' => 'operations\\Operation::comptes' dans le groupe
            protégé par opfilter (ligne 35), mais Operation::comptes() N'EXISTE PAS
            dans app/Controllers/operations/Operation.php -> route morte (erreur 404
            si appelée). Utiliser /rapports/comptes (Commission::comptes) à la place,
            ou créer la méthode manquante si la route doit vraiment vivre ici.",
    ],

    '8.5 [ROUTE MORTE] Total des frais retrait/transfert par type' => [
        'route_declaree' => "GET operations/situation/gains -> operations\\Operation::gains",
        'ATTENTION' => "operations\\Operation::gains() N'EXISTE PAS dans Operation.php.
            Le calcul existe déjà côté model : MvtModel::getGainFrais() (retourne
            libelle, total_frais, nombre_operations pour retrait+transfert) mais
            aucun controller ne l'appelle actuellement. A brancher si la page
            'situation des gains opérateur' de Taches.md doit être ré-affichée.",
        'model_pret_a_l_emploi' => [MvtModel::class, 'getGainFrais'],
    ],
];


/* =============================================================================
 * 9. API INTERNE (utilisée en AJAX par le front, pas une page)
 * =============================================================================
 * Route : GET /api/prefix -> APIController::getPrefix
 * @see app/Config/Routes.php (ligne 21)
 */
const FONCTIONNALITE_9_API = [

    '9.1 Liste des préfixes valides + opérateur (JSON)' => [
        'controller' => [APIController::class, 'getPrefix'],
        'model' => [PrefixModel::class, 'allWithOperator'],
        'consommateurs_js' => ['public/script/regex.js', 'public/script/multi.js'],
        'usage' => 'Alimente le cache prefixCache côté JS pour valider un numéro
                     sans coder les préfixes en dur dans le navigateur.',
    ],
];


/* =============================================================================
 * 10. APPARENCE GLOBALE / GABARIT COMMUN A TOUTES LES PAGES
 * =============================================================================
 * Pas une "fonctionnalité métier", mais très souvent modifié en pratique :
 */
const FONCTIONNALITE_10_APPARENCE_GLOBALE = [

    '10.1 Styles personnalisés (couleurs, cartes, boutons "-ledger")' => [
        'fichier' => include("public/css/custom.css"),                // @see public/css/custom.css
    ],

    '10.2 Bootstrap (grille, composants de base, icônes)' => [
        'fichier_css' => include("public/bootstrap/css/bootstrap.css"),     // @see public/bootstrap/css/bootstrap.css
        'fichier_js' => include("public/bootstrap/js/bootstrap.bundle.js"),  // @see public/bootstrap/js/bootstrap.bundle.js
    ],

    '10.3 Menu latéral commun (sidebar) présent sur presque toutes les pages' => [
        'vue' => include("app/Views/partials/sidebar.php"),           // @see app/Views/partials/sidebar.php
        'inclus_via' => "\$this->include('partials/sidebar')",
        'personnalisation' => 'Avatar = 2 derniers chiffres du numéro, item actif
                                déterminé par $activePage passé depuis le controller.',
    ],

    '10.4 Pages d\'erreur (404, exceptions)' => [
        'vue_404' => include("app/Views/errors/html/error_404.php"),           // @see app/Views/errors/html/error_404.php
        'vue_exception' => include("app/Views/errors/html/error_exception.php"), // @see app/Views/errors/html/error_exception.php
    ],
];


/* =============================================================================
 * RECAPITULATIF — "Je veux changer X, je vais où ?"
 * =============================================================================
 *
 *  Changer le TEXTE ou le HTML d'une page                -> app/Views/...
 *  Changer les COULEURS / STYLE                           -> public/css/custom.css
 *  Changer une VALIDATION EN TEMPS REEL (avant submit)    -> public/script/regex.js ou multi.js
 *  Changer une VALIDATION SERVEUR / règle métier           -> le Controller concerné
 *  Changer un CALCUL DE FRAIS                              -> table `tranche` (base.sql)
 *                                                              + TrancheModel::findByTypeAndMontant
 *  Changer un TAUX DE COMMISSION inter-opérateurs           -> table `comission` (base.sql)
 *                                                              + CommissionModel::findRate
 *  Ajouter un OPERATEUR / PREFIXE                           -> tables `operator` + `prefix` (base.sql)
 *  Changer QUI PEUT ACCEDER A UNE PAGE                      -> app/Config/Filters.php
 *                                                              + app/Filters/AuthFilter.php ou OPFilter.php
 *  Ajouter/retirer une URL                                  -> app/Config/Routes.php
 *  Changer une REQUETE SQL                                  -> le Model concerné
 *  Changer le MENU LATERAL                                  -> app/Views/partials/sidebar.php
 * =============================================================================
 */