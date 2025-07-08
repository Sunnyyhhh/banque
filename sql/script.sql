CREATE DATABASE banque;
USE banque;

CREATE TABLE fonds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fonds_disponibles DECIMAL(15,2) NOT NULL DEFAULT 0,
    date_ajout DATETIME
);

CREATE TABLE type_pret (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    taux DECIMAL(5,2) NOT NULL CHECK (taux >= 0 AND taux <= 100)
);

CREATE TABLE utilisateur (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    solde DECIMAL(15,2) NOT NULL DEFAULT 0,
    statut varchar(50),
    mot_de_passe VARCHAR(255) NOT NULL,
    dtn DATETIME
);

CREATE TABLE pret (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_client INT NOT NULL,
    id_type_pret INT NOT NULL,
    montant DECIMAL(15,2) NOT NULL CHECK (montant > 0),
    date_pret DATE NOT NULL,
    duree_mois INT NOT NULL CHECK (duree_mois > 0),
    date_echeance DATE NOT NULL,
    date_validation DATE NOT NULL,
    statut INT NOT NULL,
    FOREIGN KEY (id_client) REFERENCES utilisateur(id_utilisateur),
    FOREIGN KEY (id_type_pret) REFERENCES type_pret(id)
);


CREATE TABLE pret_suivi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pret INT NOT NULL,
    montant_rembourse DECIMAL(15,2) NOT NULL DEFAULT 0 CHECK (montant_rembourse >= 0),
    statut ENUM('en cours', 'rembourse', 'en retard') DEFAULT 'en cours',
    date_derniere_modif DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pret) REFERENCES pret(id)
);

CREATE TABLE remboursement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pret INT NOT NULL,
    montant DECIMAL(15,2) NOT NULL CHECK (montant > 0),
    date_remboursement DATE NOT NULL,
    interet DECIMAL(15,2)
    FOREIGN KEY (id_pret) REFERENCES pret(id)
);



INSERT INTO utilisateur (nom, prenom, email, solde, statut, mot_de_passe, dtn) VALUES
('Dupont', 'Jean', 'jean.dupont@email.com', 1000.00, 'client', 'azerty', '1990-01-01'),
('Martin', 'Sophie', 'sophie.martin@email.com', 1500.00, 'client', '123456', '1992-05-10'),
('Durand', 'Paul', 'paul.durand@email.com', 2000.00, 'client', 'motdepasse', '1988-09-15'),
('Admin', 'Super', 'admin@email.com', 0.00, 'admin', 'adminpass', '1980-12-12');