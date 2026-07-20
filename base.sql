-- =========================================================
-- STRUCTURE DE BASE CORRIGÉE ET COMPLÈTE
-- =========================================================

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

CREATE TABLE comission (
    id_op1 INT NOT NULL,
    id_op2 INT NOT NULL,
    commission_rate DECIMAL(5, 4) NOT NULL,
    PRIMARY KEY (id_op1, id_op2),
    FOREIGN KEY (id_op1) REFERENCES operator (id),
    FOREIGN KEY (id_op2) REFERENCES operator (id)
);

CREATE TABLE mvt_commission (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_mvt INT NOT NULL,
    montant DECIMAL(10, 2) NOT NULL,
    instant DATETIME NOT NULL,
    FOREIGN KEY (id_mvt) REFERENCES mvt (id)
);

-- =========================================================
-- INSERTIONS DES DONNÉES DE RÉFÉRENCE ET OPÉRATEURS
-- =========================================================

INSERT INTO operator (id, name) VALUES
    (1, 'Orange'),
    (2, 'Telma'),
    (3, 'Airtel'),
    (4, 'Blueline'),
    (5, 'Opérateur 069');

INSERT INTO prefix (value, operator_id) VALUES
    ('032', 1), ('037', 1), -- Orange
    ('034', 2), ('038', 2), -- Telma
    ('033', 3),             -- Airtel
    ('039', 4),             -- Blueline
    ('069', 5);             -- Opérateur 069

INSERT INTO type_mvt (id, libelle) VALUES
    (1, 'depot'),
    (2, 'retrait'),
    (3, 'transfert');

INSERT INTO tranche (id, inf, sup, frais, id_type) VALUES
    (1, 0, NULL, 0, 1),
    (2, 0, NULL, 500, 2),
    (3, 0, NULL, 250, 3);

-- =========================================================
-- UTILISATEURS (COMPTES CRÉDITS ET COMPTES FOURNISSEURS)
-- =========================================================

INSERT INTO user (numero, nom, solde, is_provider) VALUES
    ('0340000000', 'Fournisseur Telma', 0, 1),
    ('0320000000', 'Fournisseur Orange', 0, 1),
    ('0330000000', 'Fournisseur Airtel', 0, 1),
    ('0390000000', 'Fournisseur Blueline', 0, 1),
    ('0690000000', 'Fournisseur 069', 0, 1);

INSERT INTO user (numero, nom, solde, is_provider) VALUES
    ('0341234567', 'Aina Rakoto', 843500, 0),
    ('0327654321', 'Mika Rasoanaivo', 356200, 0),
    ('0381112233', 'Liva Andriam', 1098800, 0),
    ('0349876543', 'Solo Nirina', 214450, 0),
    ('0325556677', 'Faly Tiana', 678900, 0),
    ('0389090909', 'Miora Hery', 420100, 0),
    ('0342223344', 'Tojo Haja', 154300, 0),
    ('0321112223', 'Njiva Randria', 502300, 0),
    ('0374445556', 'Onja Ravelo', 987650, 0),
    ('0336667778', 'Hery Andrianina', 233400, 0),
    ('0338889990', 'Voahangy Rasoa', 612000, 0),
    ('0391112223', 'Fitia Andrianja', 145700, 0),
    ('0691234567', 'Rivo Nomena', 500000, 0),
    ('0698765432', 'Harija Toky', 350000, 0);

-- =========================================================
-- CONFIGURATION DES COMMISSIONS INTER-OPÉRATEURS
-- =========================================================

INSERT INTO comission (id_op1, id_op2, commission_rate) VALUES
    (1, 2, 0.0150), (2, 1, 0.0150), -- Orange <-> Telma
    (1, 3, 0.0180), (3, 1, 0.0180), -- Orange <-> Airtel
    (1, 4, 0.0200), (4, 1, 0.0200), -- Orange <-> Blueline
    (1, 5, 0.0200), (5, 1, 0.0100), -- Orange <-> 069
    
    (2, 3, 0.0120), (3, 2, 0.0120), -- Telma <-> Airtel
    (2, 4, 0.0150), (4, 2, 0.0150), -- Telma <-> Blueline
    (2, 5, 0.0200), (5, 2, 0.0100), -- Telma <-> 069
    
    (3, 4, 0.0150), (4, 3, 0.0150), -- Airtel <-> Blueline
    (3, 5, 0.0200), (5, 3, 0.0100), -- Airtel <-> 069
    
    (4, 5, 0.0200), (5, 4, 0.0100); -- Blueline <-> 069

-- =========================================================
-- MOUVEMENTS ET DÉTAILS TRANCHES
-- =========================================================

INSERT INTO mvt (id, montant, frais, id_type, num_sender, num_receiver, description, instant) VALUES
    (1, 150000, 0, 1, '0340000000', '0341234567', 'Dépôt initial', '2026-07-20 08:05:00'),
    (2, 30000, 500, 2, '0341234567', '0340000000', 'Retrait guichet', '2026-07-20 09:10:00'),
    (3, 45000, 250, 3, '0341234567', '0327654321', 'Transfert familial', '2026-07-20 10:15:00'),
    (4, 98000, 0, 1, '0340000000', '0381112233', 'Dépôt agence', '2026-07-19 16:20:00'),
    (5, 20000, 500, 2, '0381112233', '0340000000', 'Retrait express', '2026-07-19 17:35:00'),
    (6, 125000, 250, 3, '0327654321', '0341234567', 'Virement ami', '2026-07-19 18:40:00'),
    (7, 50000, 250, 3, '0341234567', '0389090909', 'Transfert loyer', '2026-07-18 14:05:00'),
    (8, 70000, 0, 1, '0340000000', '0349876543', 'Recharge portefeuille', '2026-07-18 11:15:00'),
    (9, 12000, 500, 2, '0349876543', '0340000000', 'Retrait partiel', '2026-07-17 15:30:00'),
    (10, 36000, 250, 3, '0389090909', '0325556677', 'Paiement ami', '2026-07-17 19:45:00'),
    -- Nouveaux mouvements inter-opérateurs avec '069'
    (11, 100000, 250, 3, '0341234567', '0691234567', 'Transfert vers 069', '2026-07-20 11:00:00'),
    (12, 50000, 250, 3, '0698765432', '0327654321', 'Remboursement 069', '2026-07-20 12:30:00'),
    (13, 200000, 250, 3, '0336667778', '0691234567', 'Envoi pro', '2026-07-20 13:15:00');

INSERT INTO mvt_details (id, id_mvt, id_tranche) VALUES
    (1, 1, 1), (2, 2, 2), (3, 3, 3), (4, 4, 1), (5, 5, 2),
    (6, 6, 3), (7, 7, 3), (8, 8, 1), (9, 9, 2), (10, 10, 3),
    (11, 11, 3), (12, 12, 3), (13, 13, 3);

-- =========================================================
-- CALCUL DES MOUVEMENTS DE COMMISSION (mvt_commission)
-- =========================================================

INSERT INTO mvt_commission (id, id_mvt, montant, instant) VALUES
    (1, 11, 2000.00, '2026-07-20 11:00:00'), -- 100 000 * 2.0%
    (2, 12, 500.00,  '2026-07-20 12:30:00'), -- 50 000 * 1.0%
    (3, 13, 4000.00, '2026-07-20 13:15:00'); -- 200 000 * 2.0%