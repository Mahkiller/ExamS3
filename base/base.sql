CREATE DATABASE IF NOT EXISTS Moto;
USE Moto;


CREATE TABLE IF NOT EXISTS Moto_conducteurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    salaire_pourcentage DECIMAL(5,2) NOT NULL
);


INSERT INTO Moto_conducteurs (nom, salaire_pourcentage) VALUES
('Koto', 15.00),
('Tojo', 15.00),
('Paul', 25.00),
('Rija', 25.00),
('Hery', 19.50),
('Sitraka', 19.50);


CREATE TABLE IF NOT EXISTS Moto_motos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    immatriculation VARCHAR(50) NOT NULL,
    consommation_litre_100km DECIMAL(5,2) NOT NULL,
    entretien_pourcentage DECIMAL(5,2) NOT NULL
);

INSERT INTO Moto_motos (immatriculation, consommation_litre_100km, entretien_pourcentage) VALUES
('4026-TAB', 2.00, 10.00),
('4394-TAC', 2.00, 10.00),
('4082-TBA', 2.00, 10.00),
('5123-TAD', 2.00, 10.00),
('6789-TAE', 1.60, 15.00),
('1234-TAF', 1.60, 15.00),
('5678-TAG', 1.30, 11.50),
('9101-TAH', 1.30, 11.50),
('1121-TAI', 1.30, 11.50),
('3141-TAJ', 1.30, 11.50);


CREATE TABLE IF NOT EXISTS Moto_clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    telephone VARCHAR(30),
    email VARCHAR(150),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO Moto_clients (nom, telephone, email) VALUES
('Tsinjo', '0341234567', 'tsinjo@gmail.com'),
('Mahery', '0349876543', 'mahery@gmail.com'),
('Lucas', '0335554444', 'lucas@gmail.com');


CREATE TABLE IF NOT EXISTS Moto_affectations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conducteur_id INT NOT NULL,
    moto_id INT NOT NULL,
    date_affectation DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_conducteur_date (conducteur_id, date_affectation),
    FOREIGN KEY (conducteur_id) REFERENCES Moto_conducteurs(id),
    FOREIGN KEY (moto_id) REFERENCES Moto_motos(id)
);

INSERT INTO Moto_affectations (conducteur_id, moto_id, date_affectation) VALUES
(1,1,'2025-12-01'),
(2,2,'2025-12-02'),
(3,3,'2025-12-03'),
(4,4,'2025-12-04'),
(5,5,'2025-12-05'),
(6,6,'2025-12-06');


CREATE TABLE IF NOT EXISTS Moto_courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conducteur_id INT NOT NULL,
    moto_id INT NOT NULL,
    client_id INT DEFAULT NULL,
    date_course DATE NOT NULL,
    heure_debut TIME,
    heure_fin TIME,
    km DECIMAL(6,2) NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    depart VARCHAR(100),
    arrivee VARCHAR(100),
    valide TINYINT(1) DEFAULT 0,
    prix_essence DECIMAL(8,2) DEFAULT NULL,
    FOREIGN KEY (conducteur_id) REFERENCES Moto_conducteurs(id),
    FOREIGN KEY (moto_id) REFERENCES Moto_motos(id),
    FOREIGN KEY (client_id) REFERENCES Moto_clients(id)
);

INSERT INTO Moto_courses (
    conducteur_id, moto_id, client_id, date_course, heure_debut, heure_fin,
    km, montant, depart, arrivee, valide
) VALUES
(1, 1, 1, '2025-12-01', '08:00:00', '09:15:00', 25.5, 15000, 'Analakely', 'Anosy', 0),
(2, 2, 2, '2025-12-02', '10:30:00', '11:10:00', 18.2, 12000, 'Isotry', 'Ivandry', 1),
(3, 3, 3, '2025-12-03', '14:00:00', '15:45:00', 40.0, 25000, '67Ha', 'Ambohimanarina', 1),
(4, 4, 1, '2025-12-04', '09:00:00', '09:50:00', 22.0, 14000, 'Ankorondrano', 'Ambanidia', 0),
(5, 5, 2, '2025-12-05', '16:00:00', '17:30:00', 35.8, 22000, 'Andraharo', 'Itaosy', 0);


CREATE TABLE IF NOT EXISTS Moto_parametres (
    id INT PRIMARY KEY,
    prix_essence DECIMAL(6,2) NOT NULL
);

INSERT INTO Moto_parametres (id, prix_essence) VALUES
(1, 5200.00);


CREATE TABLE IF NOT EXISTS Moto_validations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    date_validation DATE NOT NULL,
    FOREIGN KEY (course_id) REFERENCES Moto_courses(id)
);


CREATE TABLE IF NOT EXISTS Moto_modifications_courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    date_modification DATE NOT NULL,
    champ_modifie VARCHAR(100) NOT NULL,
    valeur_avant VARCHAR(255),
    valeur_apres VARCHAR(255),
    FOREIGN KEY (course_id) REFERENCES Moto_courses(id)
);

CREATE TABLE Moto_prix_essence_historique (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    prix_essence DECIMAL(6,2) NOT NULL
);