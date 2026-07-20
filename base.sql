CREATE TABLE operator (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE user (
    numero VARCHAR(10) UNIQUE PRIMARY KEY,
    nom VARCHAR(255),
    solde DECIMAL(16, 2) NOT NULL DEFAULT 0,
    is_provider INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE prefix (
    value VARCHAR(255) PRIMARY KEY,
    operator_id INT NOT NULL,
    FOREIGN KEY (operator_id) REFERENCES operator (id)
);

CREATE TABLE type_mvt (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE tranche (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    inf DECIMAL(10, 2) NOT NULL,
    sup DECIMAL(10, 2),
    frais DECIMAL(10, 2) NOT NULL DEFAULT 0,
    id_type INT NOT NULL,
    FOREIGN KEY (id_type) REFERENCES type_mvt (id)
);

CREATE TABLE mvt (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    montant DECIMAL(10, 2) NOT NULL,
    frais DECIMAL(10, 2) NOT NULL,
    id_type INT NOT NULL,
    num_sender VARCHAR(10) NOT NULL,
    num_receiver VARCHAR(10) NOT NULL,
    description TEXT(100),
    instant DATETIME NOT NULL,
    FOREIGN KEY (id_type) REFERENCES type_mvt (id),
    FOREIGN KEY (num_sender) REFERENCES user (numero),
    FOREIGN KEY (num_receiver) REFERENCES user (numero)
);

CREATE TABLE mvt_details (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_mvt INT NOT NULL,
    id_tranche INT NOT NULL,
    FOREIGN KEY (id_mvt) REFERENCES mvt (id),
    FOREIGN KEY (id_tranche) REFERENCES tranche (id)
);


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
VALUES ('0340000000', 'Fournisseur Telma', 0, 1);

-- ---------------------------------------------------------
-- Types de mouvement
-- ---------------------------------------------------------
INSERT INTO type_mvt (id, libelle)
VALUES
    (1, 'depot'),
    (2, 'retrait'),
    (3, 'transfert');





-- ---------------------------------------------------------
-- Compte fournisseur (utilisé comme contrepartie des dépôts /
-- retraits, cf. Operation::PROVIDER_NUMERO)
-- ---------------------------------------------------------
INSERT INTO user (numero, nom, solde, is_provider)
VALUES
   ('0341234567', 'Aina Rakoto', 843500, 0),
    ('0327654321', 'Mika Rasoanaivo', 356200, 0),
    ('0381112233', 'Liva Andriam', 1098800, 0),
    ('0349876543', 'Solo Nirina', 214450, 0),
    ('0325556677', 'Faly Tiana', 678900, 0),
    ('0389090909', 'Miora Hery', 420100, 0),
    ('0342223344', 'Tojo Haja', 154300, 0);


-- ---------------------------------------------------------
-- Tranches de frais de test
-- ---------------------------------------------------------
INSERT INTO tranche (id, inf, sup, frais, id_type)
VALUES
    (1, 0, NULL, 0, 1),
    (2, 0, NULL, 500, 2),
    (3, 0, NULL, 250, 3);

-- ---------------------------------------------------------
-- Mouvements de test
-- ---------------------------------------------------------
INSERT INTO mvt (id, montant, frais, id_type, num_sender, num_receiver, description, instant)
VALUES
    (1, 150000, 0, 1, '0340000000', '0341234567', 'Dépôt initial', '2026-07-20 08:05:00'),
    (2, 30000, 500, 2, '0341234567', '0340000000', 'Retrait guichet', '2026-07-20 09:10:00'),
    (3, 45000, 250, 3, '0341234567', '0327654321', 'Transfert familial', '2026-07-20 10:15:00'),
    (4, 98000, 0, 1, '0340000000', '0381112233', 'Dépôt agence', '2026-07-19 16:20:00'),
    (5, 20000, 500, 2, '0381112233', '0340000000', 'Retrait express', '2026-07-19 17:35:00'),
    (6, 125000, 250, 3, '0327654321', '0341234567', 'Virement ami', '2026-07-19 18:40:00'),
    (7, 50000, 250, 3, '0341234567', '0389090909', 'Transfert loyer', '2026-07-18 14:05:00'),
    (8, 70000, 0, 1, '0340000000', '0349876543', 'Recharge portefeuille', '2026-07-18 11:15:00'),
    (9, 12000, 500, 2, '0349876543', '0340000000', 'Retrait partiel', '2026-07-17 15:30:00'),
    (10, 36000, 250, 3, '0389090909', '0325556677', 'Paiement ami', '2026-07-17 19:45:00');

INSERT INTO mvt_details (id, id_mvt, id_tranche)
VALUES
    (1, 1, 1),
    (2, 2, 2),
    (3, 3, 3),
    (4, 4, 1),
    (5, 5, 2),
    (6, 6, 3),
    (7, 7, 3),
    (8, 8, 1),
    (9, 9, 2),
    (10, 10, 3);
