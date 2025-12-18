CREATE DATABASE Moto;
USE Moto;

CREATE TABLE Moto_conducteurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    salaire_pourcentage DECIMAL(5,2) NOT NULL
);

INSERT INTO Moto_conducteurs (nom, salaire_pourcentage) VALUES
('Koto', 10.00),
('Tojo', 12.50),
('Paul', 9.75),
('Rija', 11.00),
('Hery', 13.25);

CREATE TABLE Moto_motos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    immatriculation VARCHAR(50) NOT NULL,
    consommation_litre_100km DECIMAL(5,2) NOT NULL,
    entretien_pourcentage DECIMAL(5,2) NOT NULL
);

INSERT INTO Moto_motos (immatriculation, consommation_litre_100km, entretien_pourcentage) VALUES
('4026-TAB', 4.50, 5.00),
('4394-TAC', 3.80, 4.50),
('4082-TBA', 5.20, 6.00),
('5123-TAD', 4.10, 5.50),
('6789-TAE', 3.60, 4.20);

CREATE TABLE Moto_clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    telephone VARCHAR(30),
    email VARCHAR(150),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO Moto_clients (nom, telephone, email) VALUES
('Client A', '0341234567', 'clienta@example.com'),
('Client B', '0349876543', 'clientb@example.com'),
('Client C', '0335554444', 'clientc@example.com');

CREATE TABLE Moto_affectations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conducteur_id INT NOT NULL,
    moto_id INT NOT NULL,
    date_affectation DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_conducteur_date (conducteur_id, date_affectation),
    FOREIGN KEY (conducteur_id) REFERENCES Moto_conducteurs(id),
    FOREIGN KEY (moto_id) REFERENCES Moto_motos(id)
);

CREATE TABLE Moto_courses (
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

INSERT INTO Moto_affectations (conducteur_id, moto_id, date_affectation) VALUES
(1,1,'2025-12-01'),
(2,2,'2025-12-02'),
(3,3,'2025-12-03'),
(4,4,'2025-12-04'),
(5,5,'2025-12-05');
CREATE TABLE Moto_parametres (
    id INT PRIMARY KEY,
    prix_essence DECIMAL(6,2) NOT NULL
);  

INSERT INTO Moto_parametres (id, prix_essence) VALUES
(1, 5200.00),
(2, 5300.00),
(3, 5100.00),
(4, 5400.00),
(5, 5250.00);

CREATE TABLE Moto_validations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    date_validation DATE NOT NULL,
    FOREIGN KEY (course_id) REFERENCES Moto_courses(id)
);

INSERT INTO Moto_validations (course_id, date_validation) VALUES
(2, '2025-12-02'),
(3, '2025-12-03'),
(1, '2025-12-06'),
(4, '2025-12-06'),
(5, '2025-12-07');

CREATE TABLE Moto_modifications_courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    date_modification DATE NOT NULL,
    champ_modifie VARCHAR(100) NOT NULL,
    valeur_avant VARCHAR(255),
    valeur_apres VARCHAR(255),
    FOREIGN KEY (course_id) REFERENCES Moto_courses(id)
);