# Creation de la Base
```
Tables:
operateurs:
-id,nom

prefixe:
id, pre, id_op(fk)

frais:
-id, id_tranche, montant

tranche:
-id, inf,sup,id_type

types_operations:
-id, libelle (depot, retrait, transfert)

mvt_frais:
id,id_type, montant, montant_frais,sender,recever description

client:
-id,nom,id_op,numero

```
# Taches
- Creation du db Sqlite
- Creation de la base.sql
- Creation des tables de la base
- Creation de la page de login
  - login avec numero , no pwd
- Creation des endpoints pour:
  - landing-page
    - accueil
    - plus lien vers les actions
  - depot
    - page pour faire depot
    - automatique, format nombre
  - retrait
    - page pour faire retrait
    - automatique, format nombre
  - transfert
    - page pour faire transfert
    - choix numero(existe)
    - format nombre
  - historiques(regarder seulement id: retrait et transfert)
    - page pour regarder historique de retrait et transfert