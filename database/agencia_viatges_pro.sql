-- Base de dades del projecte Agencia de Viatges
-- Versio corregida: recrea la BBDD completa per evitar errors de foreign keys
-- IMPORTANT: executa aquest fitxer complet des del principi.

SET FOREIGN_KEY_CHECKS = 0;
DROP DATABASE IF EXISTS agencia_viatges;
SET FOREIGN_KEY_CHECKS = 1;

CREATE DATABASE agencia_viatges
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE agencia_viatges;

CREATE TABLE proveidors (
  id_proveidor INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(150) NOT NULL,
  telefon VARCHAR(50) NULL,
  correu VARCHAR(190) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE paquets (
  id_paquet INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_proveidor INT UNSIGNED NOT NULL,
  nom VARCHAR(180) NOT NULL,
  descripcio TEXT NOT NULL,
  numero_dies INT UNSIGNED NOT NULL,
  punt_origen VARCHAR(120) NOT NULL,
  continent VARCHAR(80) NOT NULL,
  pais_ruta VARCHAR(160) NOT NULL,
  galeria_url VARCHAR(500) NULL,
  pdf_circuit VARCHAR(500) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_paquets_proveidor (id_proveidor),
  CONSTRAINT fk_paquets_proveidor
    FOREIGN KEY (id_proveidor) REFERENCES proveidors(id_proveidor)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE promocions (
  id_promocio INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_paquet INT UNSIGNED NOT NULL,
  data_inici_promocio DATE NOT NULL,
  data_fi_promocio DATE NOT NULL,
  data_inici_viatge DATE NOT NULL,
  data_fi_viatge DATE NOT NULL,
  preu_base_adult DECIMAL(10,2) NOT NULL,
  preu_base_nen DECIMAL(10,2) NOT NULL,
  preu_extra_individual DECIMAL(10,2) NOT NULL DEFAULT 0,
  preu_extra_categoria_superior DECIMAL(10,2) NOT NULL DEFAULT 0,
  activa TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_promocions_paquet (id_paquet),
  INDEX idx_promocions_dates (data_inici_promocio, data_fi_promocio),
  CONSTRAINT fk_promocions_paquet
    FOREIGN KEY (id_paquet) REFERENCES paquets(id_paquet)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE clients (
  id_client INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  cognoms VARCHAR(150) NOT NULL,
  telefon VARCHAR(50) NOT NULL,
  correu VARCHAR(190) NOT NULL,
  adreca VARCHAR(255) NULL,
  document_identitat VARCHAR(50) NOT NULL,
  nacionalitat VARCHAR(80) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_clients_correu (correu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reserves (
  id_reserva INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_client INT UNSIGNED NOT NULL,
  id_promocio INT UNSIGNED NOT NULL,
  estat ENUM('PRE_RESERVA','ACCEPTADA','REBUTJADA','FORMALITZADA') NOT NULL DEFAULT 'PRE_RESERVA',
  data_reserva DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  data_formalitzacio DATETIME NULL,
  data_inici DATE NOT NULL,
  data_fi DATE NOT NULL,
  total_reserva DECIMAL(10,2) NOT NULL DEFAULT 0,
  total_pagat DECIMAL(10,2) NOT NULL DEFAULT 0,
  observacions TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_reserves_client (id_client),
  INDEX idx_reserves_promocio (id_promocio),
  INDEX idx_reserves_estat (estat),
  INDEX idx_reserves_data (data_reserva),
  CONSTRAINT fk_reserves_client
    FOREIGN KEY (id_client) REFERENCES clients(id_client)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_reserves_promocio
    FOREIGN KEY (id_promocio) REFERENCES promocions(id_promocio)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE viatgers (
  id_viatger INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_reserva INT UNSIGNED NOT NULL,
  nom VARCHAR(100) NOT NULL,
  cognoms VARCHAR(150) NOT NULL,
  adult TINYINT(1) NOT NULL DEFAULT 1,
  habitacio_individual TINYINT(1) NOT NULL DEFAULT 0,
  categoria_superior TINYINT(1) NOT NULL DEFAULT 0,
  document_identitat VARCHAR(50) NOT NULL,
  nacionalitat VARCHAR(80) NOT NULL,
  data_naixement DATE NULL,
  preferencies TEXT NULL,
  preu_calculat DECIMAL(10,2) NOT NULL DEFAULT 0,
  INDEX idx_viatgers_reserva (id_reserva),
  CONSTRAINT fk_viatgers_reserva
    FOREIGN KEY (id_reserva) REFERENCES reserves(id_reserva)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE usuaris (
  id_usuari INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  rol ENUM('ADMIN','AGENT') NOT NULL DEFAULT 'AGENT',
  actiu TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_usuaris_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notificacions_proveidor (
  id_notificacio INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_reserva INT UNSIGNED NOT NULL,
  tipus ENUM('PRE_RESERVA','ACCEPTACIO','REBUIG','FORMALITZACIO') NOT NULL,
  destinatari VARCHAR(190) NOT NULL,
  assumpte VARCHAR(190) NOT NULL,
  missatge TEXT NOT NULL,
  enviada TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_notificacions_reserva (id_reserva),
  CONSTRAINT fk_notificacions_reserva
    FOREIGN KEY (id_reserva) REFERENCES reserves(id_reserva)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO proveidors (nom, telefon, correu) VALUES
('Global Travel Wholesale', '+34 932 000 111', 'operacions@globaltravel.test'),
('Mediterranean Circuits', '+34 934 555 222', 'reservas@medcircuits.test'),
('Asia Experience Provider', '+34 936 777 333', 'booking@asiaexperience.test');

INSERT INTO paquets (id_proveidor, nom, descripcio, numero_dies, punt_origen, continent, pais_ruta, galeria_url, pdf_circuit) VALUES
(1, 'Nova York essencial', 'Circuit tancat amb vols, hotel centric i visites guiades als punts principals de Manhattan, Brooklyn i Liberty Island.', 7, 'Barcelona', 'America', 'Estats Units - Nova York', 'https://images.unsplash.com/photo-1485871981521-5b1fd3805eee?auto=format&fit=crop&w=1200&q=80', 'https://example.com/circuits/nova-york.pdf'),
(2, 'Ruta per Grecia classica', 'Paquet cultural amb Atenes, Delfos i Meteora, allotjament inclos i trasllats interns gestionats pel proveidor.', 8, 'Barcelona', 'Europa', 'Grecia', 'https://images.unsplash.com/photo-1603565816030-6b389eeb23cb?auto=format&fit=crop&w=1200&q=80', 'https://example.com/circuits/grecia.pdf'),
(3, 'Japo primavera', 'Circuit organitzat per Toquio, Kyoto i Osaka amb vols, hotels, guia local i itinerari cronologic del viatge.', 12, 'Madrid', 'Asia', 'Japo', 'https://images.unsplash.com/photo-1528164344705-47542687000d?auto=format&fit=crop&w=1200&q=80', 'https://example.com/circuits/japo.pdf');

INSERT INTO promocions (id_paquet, data_inici_promocio, data_fi_promocio, data_inici_viatge, data_fi_viatge, preu_base_adult, preu_base_nen, preu_extra_individual, preu_extra_categoria_superior, activa) VALUES
(1, '2026-05-01', '2026-12-31', '2026-09-10', '2026-09-16', 1640.00, 1280.00, 310.00, 220.00, 1),
(2, '2026-05-01', '2026-10-15', '2026-08-03', '2026-08-10', 1190.00, 890.00, 240.00, 180.00, 1),
(3, '2026-05-01', '2026-11-30', '2026-10-04', '2026-10-15', 2840.00, 2190.00, 520.00, 410.00, 1);

INSERT INTO clients (nom, cognoms, telefon, correu, adreca, document_identitat, nacionalitat) VALUES
('Laura', 'Martinez Costa', '+34 600 111 222', 'laura.martinez@example.test', 'Carrer Mallorca 100, Barcelona', '12345678A', 'Espanyola');

INSERT INTO reserves (id_client, id_promocio, estat, data_reserva, data_inici, data_fi, total_reserva, total_pagat, observacions) VALUES
(1, 2, 'PRE_RESERVA', NOW(), '2026-08-03', '2026-08-10', 2560.00, 0.00, 'Preferencia per habitacions properes.');

INSERT INTO viatgers (id_reserva, nom, cognoms, adult, habitacio_individual, categoria_superior, document_identitat, nacionalitat, data_naixement, preferencies, preu_calculat) VALUES
(1, 'Laura', 'Martinez Costa', 1, 0, 1, '12345678A', 'Espanyola', '1987-04-11', 'Menu sense gluten si es possible.', 1370.00),
(1, 'Marc', 'Martinez Costa', 1, 0, 0, '87654321B', 'Espanyola', '1985-07-20', NULL, 1190.00);

-- Password: admin123
INSERT INTO usuaris (nom, email, password_hash, rol, actiu) VALUES
('Administrador', 'admin@agencia.test', '$2y$12$FzWlR88FgDltqv.4.HgPkuy6La2ZDcR9.8JAxuyjYgbJ7yPelfWiS', 'ADMIN', 1);
