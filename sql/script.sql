create database banque;
use banque;

CREATE TABLE etablissement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    solde DECIMAL(15,2) NOT NULL DEFAULT 0,
    date_ajout datetime
);

CREATE TABLE type_pret (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    taux DECIMAL(5,2) NOT NULL 
);

CREATE TABLE client (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100),
    email VARCHAR(150),
    telephone VARCHAR(20)
);

CREATE TABLE pret (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_client INT NOT NULL,
    id_type_pret INT NOT NULL,
    id_etablissement INT NOT NULL,
    montant DECIMAL(15,2) NOT NULL,
    date_pret DATE NOT NULL,
    duree_mois INT NOT NULL,
    FOREIGN KEY (id_client) REFERENCES client(id),
    FOREIGN KEY (id_type_pret) REFERENCES type_pret(id),
    FOREIGN KEY (id_etablissement) REFERENCES etablissement(id)
);

CREATE TABLE pret_suivi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pret INT NOT NULL,
    montant_rembourse DECIMAL(15,2) NOT NULL DEFAULT 0,
    statut ENUM('en cours', 'remboursé', 'en retard') DEFAULT 'en cours',
    date_derniere_modif datetime,
    FOREIGN KEY (id_pret) REFERENCES pret(id)
);

CREATE TABLE remboursement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pret INT NOT NULL,
    montant DECIMAL(15,2) NOT NULL,
    date_remboursement DATE NOT NULL,
    FOREIGN KEY (id_pret) REFERENCES pret(id)
);


