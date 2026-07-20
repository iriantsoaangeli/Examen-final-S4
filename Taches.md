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
- Creation du db Sqlite (Angeli)
- Creation de la base.sql (Angeli)
- Creation des tables de la base (Angeli)
- Creation de la page de login (Angeli)
  - login avec numero , no pwd (Angeli)
- Creation des endpoints pour: (Angeli)
  - landing-page
    - accueil
    - plus lien vers les actions
  - depot (Randy)
    - page pour faire depot
    - automatique, format nombre
  - retrait (Randy)
    - page pour faire retrait
    - automatique, format nombre
  - transfert (Randy)
    - page pour faire transfert
    - choix numero(existe)
    - format nombre
  - historiques(regarder seulement id: retrait et transfert) (Randy)
    - page pour regarder historique de retrait et transfert