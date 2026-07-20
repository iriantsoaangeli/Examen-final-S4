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
-