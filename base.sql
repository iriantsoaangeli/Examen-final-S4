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

INSERT INTO operator (id, name)
VALUES (1, 'Operateur Mobile Money');

INSERT INTO user (numero, nom, solde, is_provider)
VALUES ('034000000', 'Fournisseur invisible', 0, 1);

INSERT INTO prefix (value, operator_id)
VALUES ('033', 1), ('037', 1);

INSERT INTO type_mvt (id, libelle)
VALUES (1, 'depot'), (2, 'retrait'), (3, 'transfert');
