
-- CREATE DATABASE plateforme_artisans;
-- USE plateforme_artisans;

-- 1. Tables Indépendantes / Géographiques
CREATE TABLE Wilaya (
    idWilaya INT AUTO_INCREMENT PRIMARY KEY,
    nomWilaya VARCHAR(100) NOT NULL
);

CREATE TABLE Ville (
    idVille INT AUTO_INCREMENT PRIMARY KEY,
    nomVille VARCHAR(100) NOT NULL,
    idWilaya INT NOT NULL,
    FOREIGN KEY (idWilaya) REFERENCES Wilaya(idWilaya) ON DELETE CASCADE
);

-- 2. Gestion des Utilisateurs et Héritage
CREATE TABLE Utilisateur (
    idUtilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nomComplet VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    motDePasse VARCHAR(255) NOT NULL,
    dateCreation DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(50) DEFAULT 'Actif'
);

CREATE TABLE Administrateur (
    idUtilisateur INT PRIMARY KEY,
    FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur(idUtilisateur) ON DELETE CASCADE
);

CREATE TABLE Client (
    idUtilisateur INT PRIMARY KEY,
    idVille INT NOT NULL,
    FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur(idUtilisateur) ON DELETE CASCADE,
    FOREIGN KEY (idVille) REFERENCES Ville(idVille)
);

CREATE TABLE Artisan (
    idUtilisateur INT PRIMARY KEY,
    estVerifie BOOLEAN DEFAULT FALSE,
    dateVerification DATETIME NULL,
    FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur(idUtilisateur) ON DELETE CASCADE
);

-- 3. Profils, Disponibilités et Favoris
CREATE TABLE ProfilArtisan (
    idProfil INT AUTO_INCREMENT PRIMARY KEY,
    idArtisan INT UNIQUE NOT NULL,
    description TEXT,
    adresse VARCHAR(255),
    photoProfil VARCHAR(255),
    photoCouverture VARCHAR(255),
    anneesExperience INT,
    facebook VARCHAR(100),
    instagram VARCHAR(100),
    tiktok VARCHAR(100),
    whatsapp VARCHAR(100),
    qualification VARCHAR(150),
    diplome TEXT,
    urgence BOOLEAN DEFAULT FALSE,
    idVille INT NOT NULL,
    FOREIGN KEY (idArtisan) REFERENCES Artisan(idUtilisateur) ON DELETE CASCADE,
    FOREIGN KEY (idVille) REFERENCES Ville(idVille)
);

CREATE TABLE Disponibilite (
    idDisponibilite INT AUTO_INCREMENT PRIMARY KEY,
    idArtisan INT NOT NULL,
    jourSemaine VARCHAR(15) NOT NULL,
    heureDebut TIME NOT NULL,
    heureFin TIME NOT NULL,
    actif BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (idArtisan) REFERENCES Artisan(idUtilisateur) ON DELETE CASCADE
);

CREATE TABLE Favori (
    idFavori INT AUTO_INCREMENT PRIMARY KEY,
    idClient INT NOT NULL,
    idArtisan INT NOT NULL,
    dateAjout DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT unique_favori UNIQUE (idClient, idArtisan),
    FOREIGN KEY (idClient) REFERENCES Client(idUtilisateur) ON DELETE CASCADE,
    FOREIGN KEY (idArtisan) REFERENCES Artisan(idUtilisateur) ON DELETE CASCADE
);

-- 4. Catalogue des Services
CREATE TABLE Categorie (
    idCategorie INT AUTO_INCREMENT PRIMARY KEY,
    nomCategorie VARCHAR(100) NOT NULL
);

CREATE TABLE Service (
    idService INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    description TEXT,
    prixBase DECIMAL(10, 2) NOT NULL,
    idCategorie INT NOT NULL,
    FOREIGN KEY (idCategorie) REFERENCES Categorie(idCategorie) ON DELETE RESTRICT
);

-- 5. Tarification Spécifique (Classe Association Prestation)
CREATE TABLE Prestation (
    idPrestation INT AUTO_INCREMENT PRIMARY KEY,
    idArtisan INT NOT NULL,
    idService INT NOT NULL,
    prixArtisan DECIMAL(10, 2) NOT NULL,
    dureeEstimee INT NOT NULL, -- En minutes
    CONSTRAINT unique_prestation_artisan UNIQUE (idArtisan, idService),
    FOREIGN KEY (idArtisan) REFERENCES Artisan(idUtilisateur) ON DELETE CASCADE,
    FOREIGN KEY (idService) REFERENCES Service(idService) ON DELETE CASCADE
);

-- 6. Réservations et Avis Sécurisés
CREATE TABLE Reservation (
    idReservation INT AUTO_INCREMENT PRIMARY KEY,
    idClient INT NOT NULL,
    idPrestation INT NOT NULL,
    dateHeureIntervention DATETIME NOT NULL,
    statut VARCHAR(50) DEFAULT 'En attente', -- En attente, Confirmée, Terminée, Annulée
    prixTotal DECIMAL(10, 2) NOT NULL,
    adresseIntervention VARCHAR(255) NOT NULL,
    FOREIGN KEY (idClient) REFERENCES Client(idUtilisateur) ON DELETE RESTRICT,
    FOREIGN KEY (idPrestation) REFERENCES Prestation(idPrestation) ON DELETE RESTRICT
);

CREATE TABLE Avis (
    idAvis INT AUTO_INCREMENT PRIMARY KEY,
    idReservation INT UNIQUE NOT NULL, -- Contrainte UNIQUE : 1 seul avis par réservation consommée
    note INT CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT,
    dateCreation DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(50) DEFAULT 'Affiché',
    FOREIGN KEY (idReservation) REFERENCES Reservation(idReservation) ON DELETE CASCADE
);
