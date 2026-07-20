# Création de la base

```txt
Tables :

operator :
- id, name

prefix :
- value, operator_id(fk)

type_mvt :
- id, libelle (depot, retrait, transfert)

tranche :
- id, inf, sup, frais, id_type(fk)

user :
- numero(pk), nom, solde, is_provider
- fournisseur invisible : numero = 000000000

mvt :
- id, id_type(fk), montant, frais, num_sender(fk), num_receiver(fk), description, instant

mvt_details :
- id, id_mvt(fk), id_tranche(fk)
```

# Règles métier

- Les préfixes valides de l'opérateur sont configurables (`033`, `037`, etc.).
- Les types d'opérations sont : dépôt, retrait, transfert.
- Les frais sont calculés avec les tranches liées au type d'opération.
- Les dépôts et retraits passent automatiquement par le fournisseur invisible.
- Le fournisseur invisible utilise le numéro `000000000`.
- Les gains opérateur viennent des frais de retrait et de transfert.
- Les comptes clients excluent le fournisseur invisible.

# Tâches

- [x] Création du db Sqlite                                         (Angeli)
- [x] Création de la base.sql                                       (Angeli)
- [x] Création des tables de la base                                (Angeli)
- [x] Correction logique table `user`                               (Angeli)
  - [x] `numero` comme clé primaire
  - [x] ajouter `nom`, `solde`, `is_provider`
  - [x] créer le fournisseur invisible
- [x] Correction logique table `mvt`                                (Randy)
  - [x] utiliser `num_sender` et `num_receiver`
  - [x] lier les mouvements aux numéros clients
  - [x] stocker montant, frais, type, description et date
- [x] Ajout de la table `mvt_details`                               (Randy)
  - [x] lier un mouvement à sa tranche de frais
- [x] Création des models opérations                                (Randy)
  - [x] `UserModel`                                                 (Randy)
  - [x] `MvtModel`                                                  (Randy)
  - [x] `MvtDetailsModel`                                           (Randy)
  - [x] `TrancheModel`                                              (Randy)
  - [x] `TypeMvtModel`                                              (Randy)
  - [x] `PrefixModel`                                               (Randy)
- [x] Création du controller opérations                             (Randy)
  - [x] dépôt
  - [x] retrait
  - [x] transfert
  - [x] historique client
  - [x] situation des gains opérateur
  - [x] situation des comptes clients
- [x] Création de la page de login                                  (Angeli)
  - [x] créer template                                              (Angeli)
  - [x] login avec numero, no pwd                                   (Angeli)
    - [x] créer un fichier regex.js                                 (Angeli)
      - [x] créer la fonction vérifier numéro                       (Angeli)
      - [x] créer la fonction vérifier préfixe en ajax              (Angeli)
  - [x] Authentification                                            (Angeli)
    - [x] créer authfilter                                          (Angeli)
    - [x] ajouter authfilter à filters.php                          (Angeli)
- [x] Création des endpoints pour :
  - [x] landing-page                                                (Angeli)
    - [x] accueil
    - [x] liens vers les actions
  - [x] dépôt                                                       (Randy)
    - [x] page pour faire dépôt
    - [x] validation du numéro client
    - [x] affichage du montant et des frais
    - [x] reçu après opération
  - [x] retrait                                                     (Randy)
    - [x] page pour faire retrait
    - [x] validation solde suffisant
    - [x] affichage du montant et des frais
    - [x] reçu après opération
  - [x] transfert                                                   (Randy)
    - [x] page pour faire transfert
    - [x] choix numéro destinataire existant
    - [x] validation solde suffisant
    - [x] affichage du montant et des frais
  - [x] historiques                                                 (Randy)
    - [x] page pour regarder historique retrait et transfert
    - [x] filtrer par client
    - [x] afficher date, montant, frais et type
  - [x] situation opérateur                                         (Randy)
    - [x] total frais retrait
    - [x] total frais transfert
    - [x] nombre d'opérations par type
  - [x] situation comptes clients                                   (Angeli)
    - [x] liste des clients
    - [x] numéro, nom et solde
    - [x] masquer le fournisseur invisible
  - [ ] Mise a jour base de donnees                                 
    - [ ] Ajouter d'autres operateur                                (Angeli)
      - [ ] Ajouter Airtel,Tout les numeros yas                     (Angeli)
  - [ ] Creer le filtre OPFilter.php
    - [ ] Empecher l'acces a la deuxieme page d'envoi 
    - [ ] Creer une copie de la page envoi, envoi                   (Angeli)
        multiple disponible                 
  - [ ] Modifier Controllers\operations\Operation pour              (Angeli)
        splitter lors d'envoi multiple  
  - [ ] Creer la page Situation gain via differents frais           (Randy)
    - [ ] Ajouter situation de montant pour chaque operateur        (Randy)
    - [ ] Creer le controller app/Controllers/rapports/Rapport.php  (Randy)
      - [ ] Fonction: 
        - [ ] - gainsParOperateurs(gains pour chaques operateurs)
        - [ ] - montantsAEnvoyer(du aux autres operateurs)
---