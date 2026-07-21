# Carte du projet CASH

Plan de navigation du projet, avec des liens cliquables vers les fichiers et les fonctions exactes. L'objectif est simple: savoir immédiatement où aller modifier le code.

## Plan

1. [Base de données](#0-base-de-donnees)
2. [Authentification](#1-authentification)
3. [Tableau de bord](#2-tableau-de-bord)
4. [Dépôt](#3-depot)
5. [Retrait](#4-retrait)
6. [Transfert simple et multiple](#5-transfert-simple-et-multiple)
7. [Historique des transactions](#6-historique-des-transactions)
8. [Reçu](#7-recu)
9. [Rapports](#8-rapports)
10. [API interne](#9-api-interne)
11. [Apparence globale](#10-apparence-globale)
12. [Résumé rapide](#resume-rapide)

## Comment lire ce document

- [Routes](app/Config/Routes.php) indique l'entrée HTTP.
- Le lien sur la fonction va directement à la ligne exacte du controller ou du model.
- Les vues sont liées par fichier, car l'intérêt principal est d'ouvrir le bon écran.
- Quand une fonctionnalité a plusieurs étapes, elles sont découpées en sous-fonctionnalités.

## 0. Base de données

Référence principale: [base.sql](base.sql)

Les modèles associés sont: [UserModel](app/Models/UserModel.php), [MvtModel](app/Models/MvtModel.php), [MvtDetailsModel](app/Models/MvtDetailsModel.php), [PrefixModel](app/Models/PrefixModel.php), [TrancheModel](app/Models/TrancheModel.php), [TypeMvtModel](app/Models/TypeMvtModel.php), [CommissionModel](app/Models/CommissionModel.php).

## 1. Authentification

Route d'entrée: [app/Config/Routes.php#L10](app/Config/Routes.php#L10), [app/Config/Routes.php#L13](app/Config/Routes.php#L13)

### 1.1 Affichage du formulaire de login

- Vue: [app/Views/login/login.php](app/Views/login/login.php)
- Controller: [login()](app/Controllers/AuthController.php#L10)

### 1.2 Validation du numéro

- Controller de validation: [regex()](app/Controllers/AuthController.php#L49)
- Règle métier côté données: [isValidNumero()](app/Models/PrefixModel.php#L14)

### 1.3 Validation live côté navigateur

- Script: [public/script/regex.js](public/script/regex.js)
- API consommée: [getPrefix()](app/Controllers/APIController.php#L15)

### 1.4 Création automatique du compte si inconnu

- Controller: [authenticate()](app/Controllers/AuthController.php#L15)
- Vérification d'existence: [exists()](app/Models/UserModel.php#L33)
- Création: [createUser()](app/Models/UserModel.php#L38)

### 1.5 Ouverture de session

- Controller: [authenticate()](app/Controllers/AuthController.php#L15)
- Lecture du compte: [findByNumero()](app/Models/UserModel.php#L21)

### 1.6 Déconnexion

- Controller: [logout()](app/Controllers/AuthController.php#L38)

### 1.7 Protection des pages

- Filtre: [before()](app/Filters/AuthFilter.php#L11)
- Configuration: [app/Config/Filters.php](app/Config/Filters.php)

## 2. Tableau de bord

Route d'entrée: [app/Config/Routes.php#L15](app/Config/Routes.php#L15), [app/Config/Routes.php#L16](app/Config/Routes.php#L16)

### 2.1 Affichage du solde et des infos du compte

- Vue: [app/Views/home/dashboard.php](app/Views/home/dashboard.php)
- Controller: [dashboard()](app/Controllers/Home.php#L10)
- Données utilisateur: [findByNumero()](app/Models/UserModel.php#L21)

### 2.2 Liste des 3 dernières transactions

- Controller: [dashboard()](app/Controllers/Home.php#L10)
- Source des transactions: [getRecentForUser()](app/Models/MvtModel.php#L38)

### 2.3 Menu latéral

- Vue partagée: [app/Views/partials/sidebar.php](app/Views/partials/sidebar.php)

## 3. Dépôt

Route d'entrée: [app/Config/Routes.php#L25](app/Config/Routes.php#L25), [app/Config/Routes.php#L26](app/Config/Routes.php#L26)

### 3.1 Formulaire de dépôt

- Vue: [app/Views/operations/depot.php](app/Views/operations/depot.php)
- Controller: [depot()](app/Controllers/operations/Operation.php#L47)
- Rendu commun: [renderOperationPage()](app/Controllers/operations/Operation.php#L94)

### 3.2 Validation du bénéficiaire

- Controller: [resoudreParticipants()](app/Controllers/operations/Operation.php#L217)
- Recherche client: [findClientByNumero()](app/Controllers/operations/Operation.php#L290)
- Validation de préfixe: [isValidNumero()](app/Models/PrefixModel.php#L14)

### 3.3 Calcul du montant et des frais

- Calcul: [calculerDebit()](app/Controllers/operations/Operation.php#L168)
- Barème: [findByTypeAndMontant()](app/Models/TrancheModel.php#L14)

### 3.4 Enregistrement du mouvement

- Controller: [enregistrerMouvement()](app/Controllers/operations/Operation.php#L244)
- Mise à jour de solde: [incrementerSolde()](app/Controllers/operations/Operation.php#L311)
- Écriture mouvement: [MvtModel](app/Models/MvtModel.php) et [MvtDetailsModel](app/Models/MvtDetailsModel.php)

### 3.5 Reçu après dépôt

- Construction du reçu: [buildReceiptData()](app/Controllers/operations/Operation.php#L184)
- Page de reçu: [app/Views/operations/recu.php](app/Views/operations/recu.php)

## 4. Retrait

Route d'entrée: [app/Config/Routes.php#L27](app/Config/Routes.php#L27), [app/Config/Routes.php#L28](app/Config/Routes.php#L28)

### 4.1 Formulaire de retrait

- Vue: [app/Views/operations/retrait.php](app/Views/operations/retrait.php)
- Controller: [retrait()](app/Controllers/operations/Operation.php#L56)

### 4.2 Receiver fournisseur invisible

- Controller: [resoudreParticipants()](app/Controllers/operations/Operation.php#L217)
- Source fournisseur: [getProviderNumero()](app/Controllers/operations/Operation.php#L38)
- Donnée fournisseur: [getProviderNumero()](app/Models/UserModel.php#L13)

### 4.3 Vérification du solde suffisant

- Controller: [verifierSolde()](app/Controllers/operations/Operation.php#L177)

### 4.4 Frais de retrait

- Calcul: [calculerDebit()](app/Controllers/operations/Operation.php#L168)
- Barème: [findByTypeAndMontant()](app/Models/TrancheModel.php#L14)

### 4.5 Pas de crédit au receiver

- Enregistrement: [enregistrerMouvement()](app/Controllers/operations/Operation.php#L244)

## 5. Transfert simple et multiple

Route transfert simple: [app/Config/Routes.php#L31](app/Config/Routes.php#L31), [app/Config/Routes.php#L32](app/Config/Routes.php#L32)

Route transfert multiple: [app/Config/Routes.php#L38](app/Config/Routes.php#L38), [app/Config/Routes.php#L39](app/Config/Routes.php#L39)

### 5.1 Transfert simple

- Vue: [app/Views/operations/transfert.php](app/Views/operations/transfert.php)
- Controller: [transfert()](app/Controllers/operations/Operation.php#L65)

### 5.2 Validation du destinataire

- Controller: [resoudreParticipants()](app/Controllers/operations/Operation.php#L217)
- Recherche client: [findClientByNumero()](app/Controllers/operations/Operation.php#L290)
- Validation préfixe: [isValidNumero()](app/Models/PrefixModel.php#L14)
- Recherche compte: [findByNumero()](app/Models/UserModel.php#L21)

### 5.3 Solde suffisant

- Contrôle: [verifierSolde()](app/Controllers/operations/Operation.php#L177)

### 5.4 Frais de transfert

- Résolution de tranche: [resoudreTranche()](app/Controllers/operations/Operation.php#L208)
- Barème: [findByTypeAndMontant()](app/Models/TrancheModel.php#L14)

### 5.5 Commission inter-opérateurs

- Calcul: [calculerCommissionInterOperateur()](app/Controllers/operations/Operation.php#L478)
- Détection opérateur source: [getOperatorIdByPrefix()](app/Controllers/operations/Operation.php#L502)
- Taux de commission: [getCommissionRate()](app/Controllers/operations/Operation.php#L513)
- Lecture du taux: [findRate()](app/Models/CommissionModel.php#L88)
- Enregistrement: [enregistrerCommissionInterOperateur()](app/Controllers/operations/Operation.php#L468)
- Persistance: [enregistrerCommission()](app/Models/CommissionModel.php#L99)

### 5.6 Filtre OP

- Filtre: [before()](app/Filters/OPFilter.php#L11)
- Groupe de routes: [app/Config/Routes.php#L30](app/Config/Routes.php#L30)

### 5.7 Transfert multiple

- Vue: [app/Views/operations/transfert-multiple.php](app/Views/operations/transfert-multiple.php)
- Controller: [transfertMultiple()](app/Controllers/operations/Operation.php#L322)
- Script: [public/script/multi.js](public/script/multi.js)

### 5.8 Préparation du transfert multiple

- Préparation: [preparerTransfertMultiple()](app/Controllers/operations/Operation.php#L340)

### 5.9 Vérification globale du solde

- Préparation: [preparerTransfertMultiple()](app/Controllers/operations/Operation.php#L340)

### 5.10 Exécution sur chaque destinataire

- Exécution globale: [executerTransfertMultiple()](app/Controllers/operations/Operation.php#L383)
- Traitement d'un destinataire: [traiterDestinataireTransfertMultiple()](app/Controllers/operations/Operation.php#L410)
- Écriture mouvement: [MvtModel](app/Models/MvtModel.php) et [MvtDetailsModel](app/Models/MvtDetailsModel.php)

### 5.11 Reçu du transfert multiple

- Construction du reçu: [buildTransfertMultipleReceipt()](app/Controllers/operations/Operation.php#L454)

## 6. Historique des transactions

Route HTML: [app/Config/Routes.php#L17](app/Config/Routes.php#L17), [app/Config/Routes.php#L18](app/Config/Routes.php#L18)

Route JSON historique: [app/Config/Routes.php#L33](app/Config/Routes.php#L33)

### 6.1 Vue historique paginée

- Vue: [app/Views/history/historique.php](app/Views/history/historique.php)
- Pagination: [app/Views/pager/ledger_pagination.php](app/Views/pager/ledger_pagination.php)
- Controller: [transactions()](app/Controllers/Home.php#L62)
- Données paginées: [getPaginatedForUser()](app/Models/MvtModel.php#L56)

### 6.2 Groupement par date et libellé humain

- Controller: [transactions()](app/Controllers/Home.php#L62)
- Helper date: [moisFr()](app/Controllers/Home.php#L131)

### 6.3 Endpoint JSON par client

- Controller: [historique()](app/Controllers/operations/Operation.php#L84)
- Données: [getMouvementsByUser()](app/Models/MvtModel.php#L20)

## 7. Reçu

Route d'entrée: [app/Config/Routes.php#L19](app/Config/Routes.php#L19), [app/Config/Routes.php#L20](app/Config/Routes.php#L20), [app/Config/Routes.php#L21](app/Config/Routes.php#L21)

### 7.1 Page de reçu

- Vue: [app/Views/operations/recu.php](app/Views/operations/recu.php)
- Controller: [recu()](app/Controllers/operations/Operation.php#L74)

### 7.2 Reçu standard

- Construction: [buildReceiptData()](app/Controllers/operations/Operation.php#L184)

### 7.3 Reçu transfert multiple

- Construction: [buildTransfertMultipleReceipt()](app/Controllers/operations/Operation.php#L454)

## 8. Rapports

Routes: [app/Config/Routes.php#L41](app/Config/Routes.php#L41), [app/Config/Routes.php#L42](app/Config/Routes.php#L42), [app/Config/Routes.php#L43](app/Config/Routes.php#L43), [app/Config/Routes.php#L44](app/Config/Routes.php#L44)

### 8.1 Rapport principal

- Vue: [app/Views/rapports/commission.php](app/Views/rapports/commission.php)
- Controller: [index()](app/Controllers/rapports/Commission.php#L23)
- Données commissions: [getCommissionsParOperateur()](app/Models/CommissionModel.php#L14)
- Montants à envoyer: [getMontantsAEnvoyerParOperateur()](app/Models/CommissionModel.php#L40)

### 8.2 Barème des commissions

- Vue: [app/Views/rapports/bareme.php](app/Views/rapports/bareme.php)
- Controller: [bareme()](app/Controllers/rapports/Commission.php#L32)
- Configuration: [getConfigurationCommissions()](app/Models/CommissionModel.php#L62)

### 8.3 Liste des opérateurs

- Vue: [app/Views/rapports/operateurs.php](app/Views/rapports/operateurs.php)
- Controller: [operateurs()](app/Controllers/rapports/Commission.php#L40)
- Données opérateurs: [operatorsWithProviders()](app/Models/PrefixModel.php#L43)

### 8.4 Situation des comptes clients

- Controller: [comptes()](app/Controllers/rapports/Commission.php#L48)
- Données clients: [getClients()](app/Models/UserModel.php#L26)

## 9. API interne

Route: [app/Config/Routes.php#L23](app/Config/Routes.php#L23)

### 9.1 Préfixes et opérateurs

- Controller: [getPrefix()](app/Controllers/APIController.php#L15)
- Données: [allWithOperator()](app/Models/PrefixModel.php#L25)
- Consommateurs front: [public/script/regex.js](public/script/regex.js) et [public/script/multi.js](public/script/multi.js)

## 10. Apparence globale

### 10.1 CSS principal

- [public/css/custom.css](public/css/custom.css)

### 10.2 Bootstrap

- CSS: [public/bootstrap/css/bootstrap.css](public/bootstrap/css/bootstrap.css)
- JS: [public/bootstrap/js/bootstrap.bundle.js](public/bootstrap/js/bootstrap.bundle.js)

### 10.3 Sidebar commune

- Vue: [app/Views/partials/sidebar.php](app/Views/partials/sidebar.php)

### 10.4 Pages d'erreur

- [app/Views/errors/html/error_404.php](app/Views/errors/html/error_404.php)
- [app/Views/errors/html/error_exception.php](app/Views/errors/html/error_exception.php)

## Resume rapide

- HTML ou texte: [app/Views](app/Views)
- Navigation ou logique: [app/Controllers](app/Controllers)
- Données ou SQL: [app/Models](app/Models)
- Routes: [app/Config/Routes.php](app/Config/Routes.php)
- Filtres: [app/Config/Filters.php](app/Config/Filters.php)
- Style: [public/css/custom.css](public/css/custom.css)
- Scripts: [public/script](public/script)
