CREATE TABLE Moto_conducteurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    salaire_pourcentage DECIMAL(5,2) NOT NULL
);

INSERT INTO Moto_conducteurs (nom, salaire_pourcentage) VALUES
('Koto', 10.00),
('Tojo', 12.50),
('Paul', 9.75);

CREATE TABLE Moto_motos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    immatriculation VARCHAR(50) NOT NULL,
    consommation_litre_100km DECIMAL(5,2) NOT NULL,
    entretien_pourcentage DECIMAL(5,2) NOT NULL
);

INSERT INTO Moto_motos (immatriculation, consommation_litre_100km, entretien_pourcentage) VALUES
('4026-TAB', 4.50, 5.00),
('4394-TAC', 3.80, 4.50),
('4082-TBA', 5.20, 6.00);

CREATE TABLE Moto_courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conducteur_id INT NOT NULL,
    moto_id INT NOT NULL,
    date_course DATE NOT NULL,
    heure_debut TIME,
    heure_fin TIME,
    km DECIMAL(6,2) NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    depart VARCHAR(100),
    arrivee VARCHAR(100),
    valide TINYINT(1) DEFAULT 0,
    FOREIGN KEY (conducteur_id) REFERENCES Moto_conducteurs(id),
    FOREIGN KEY (moto_id) REFERENCES Moto_motos(id)
);

CREATE TABLE Moto_parametres (
    id INT PRIMARY KEY,
    prix_essence DECIMAL(6,2) NOT NULL
);  
