CREATE TABLE operator (
    id INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE user (
    numero VARCHAR(10) PRIMARY KEY
);

CREATE TABLE prefix (
    value VARCHAR(255) PRIMARY KEY,
    operator_id INT NOT NULL,
    FOREIGN KEY (operator_id) REFERENCES operator (id)
);

CREATE TABLE type_mvt (
    id int PRIMARY KEY,
    libelle VARCHAR(30)
);

CREATE TABLE tranche (
    id INT PRIMARY KEY,
    inf DECIMAL(10, 2),
    sup DECIMAL(10, 2),
    id_type INT NOT NULL,
    FOREIGN KEY (id_type) REFERENCES type_mvt (id)
);

CREATE TABLE mvt (
    id INT PRIMARY KEY,
    montant DECIMAL(10, 2) NOT NULL,
    frais DECIMAL(10, 2) NOT NULL,
    id_type INT NOT NULL,
    num_sender VARCHAR(10),
    num_receiver VARCHAR(10),
    description TEXT(100),
    instant DATETIME NOT NULL,
    FOREIGN KEY (id_type) REFERENCES type_mvt (id),
    FOREIGN KEY (num_sender) REFERENCES user (numero),
    FOREIGN KEY (num_receiver) REFERENCES user (numero)
);