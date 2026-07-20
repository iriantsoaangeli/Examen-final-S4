
-- ---------------------------------------------------------
-- Opérateurs
-- ---------------------------------------------------------
INSERT INTO operator (id, name)
VALUES
    (1, 'Orange'),
    (2, 'Telma');

-- ---------------------------------------------------------
-- Préfixes rattachés aux opérateurs
--   032          -> Orange
--   034 / 038    -> Telma
-- ---------------------------------------------------------
INSERT INTO prefix (value, operator_id)
VALUES
    ('032', 1),
    ('034', 2),
    ('038', 2);

-- ---------------------------------------------------------
-- Compte fournisseur (utilisé comme contrepartie des dépôts /
-- retraits, cf. Operation::PROVIDER_NUMERO)
-- ---------------------------------------------------------
INSERT INTO user (numero, nom, solde, is_provider)
VALUES ('0340000001', 'Fournisseur Telma', 0, 1);

-- ---------------------------------------------------------
-- Types de mouvement
-- ---------------------------------------------------------
INSERT INTO type_mvt (id, libelle)
VALUES
    (1, 'depot'),
    (2, 'retrait'),
    (3, 'transfert');
