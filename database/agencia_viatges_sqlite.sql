-- Base de dades SQLite del projecte Agencia de Viatges

PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS proveidors (
  id_proveidor INTEGER PRIMARY KEY AUTOINCREMENT,
  nom TEXT NOT NULL,
  telefon TEXT NULL,
  correu TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS paquets (
  id_paquet INTEGER PRIMARY KEY AUTOINCREMENT,
  id_proveidor INTEGER NOT NULL,
  nom TEXT NOT NULL,
  descripcio TEXT NOT NULL,
  numero_dies INTEGER NOT NULL,
  punt_origen TEXT NOT NULL,
  continent TEXT NOT NULL,
  pais_ruta TEXT NOT NULL,
  galeria_url TEXT NULL,
  pdf_circuit TEXT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_proveidor) REFERENCES proveidors(id_proveidor) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS promocions (
  id_promocio INTEGER PRIMARY KEY AUTOINCREMENT,
  id_paquet INTEGER NOT NULL,
  data_inici_promocio DATE NOT NULL,
  data_fi_promocio DATE NOT NULL,
  data_inici_viatge DATE NOT NULL,
  data_fi_viatge DATE NOT NULL,
  preu_base_adult REAL NOT NULL,
  preu_base_nen REAL NOT NULL,
  preu_extra_individual REAL NOT NULL DEFAULT 0,
  preu_extra_categoria_superior REAL NOT NULL DEFAULT 0,
  activa INTEGER NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_paquet) REFERENCES paquets(id_paquet) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS clients (
  id_client INTEGER PRIMARY KEY AUTOINCREMENT,
  nom TEXT NOT NULL,
  cognoms TEXT NOT NULL,
  telefon TEXT NOT NULL,
  correu TEXT NOT NULL UNIQUE,
  adreca TEXT NULL,
  document_identitat TEXT NOT NULL,
  nacionalitat TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reserves (
  id_reserva INTEGER PRIMARY KEY AUTOINCREMENT,
  id_client INTEGER NOT NULL,
  id_promocio INTEGER NOT NULL,
  estat TEXT NOT NULL DEFAULT 'PRE_RESERVA',
  data_reserva DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  data_formalitzacio DATETIME NULL,
  data_inici DATE NOT NULL,
  data_fi DATE NOT NULL,
  total_reserva REAL NOT NULL DEFAULT 0,
  total_pagat REAL NOT NULL DEFAULT 0,
  observacions TEXT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_client) REFERENCES clients(id_client) ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (id_promocio) REFERENCES promocions(id_promocio) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS viatgers (
  id_viatger INTEGER PRIMARY KEY AUTOINCREMENT,
  id_reserva INTEGER NOT NULL,
  nom TEXT NOT NULL,
  cognoms TEXT NOT NULL,
  adult INTEGER NOT NULL DEFAULT 1,
  habitacio_individual INTEGER NOT NULL DEFAULT 0,
  categoria_superior INTEGER NOT NULL DEFAULT 0,
  document_identitat TEXT NOT NULL,
  nacionalitat TEXT NOT NULL,
  data_naixement DATE NULL,
  preferencies TEXT NULL,
  preu_calculat REAL NOT NULL DEFAULT 0,
  FOREIGN KEY (id_reserva) REFERENCES reserves(id_reserva) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS usuaris (
  id_usuari INTEGER PRIMARY KEY AUTOINCREMENT,
  nom TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  rol TEXT NOT NULL DEFAULT 'AGENT',
  actiu INTEGER NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS notificacions_proveidor (
  id_notificacio INTEGER PRIMARY KEY AUTOINCREMENT,
  id_reserva INTEGER NOT NULL,
  tipus TEXT NOT NULL,
  destinatari TEXT NOT NULL,
  assumpte TEXT NOT NULL,
  missatge TEXT NOT NULL,
  enviada INTEGER NOT NULL DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_reserva) REFERENCES reserves(id_reserva) ON UPDATE CASCADE ON DELETE CASCADE
);

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
(1, 2, 'PRE_RESERVA', CURRENT_TIMESTAMP, '2026-08-03', '2026-08-10', 2560.00, 0.00, 'Preferencia per habitacions properes.');

INSERT INTO viatgers (id_reserva, nom, cognoms, adult, habitacio_individual, categoria_superior, document_identitat, nacionalitat, data_naixement, preferencies, preu_calculat) VALUES
(1, 'Laura', 'Martinez Costa', 1, 0, 1, '12345678A', 'Espanyola', '1987-04-11', 'Menu sense gluten si es possible.', 1370.00),
(1, 'Marc', 'Martinez Costa', 1, 0, 0, '87654321B', 'Espanyola', '1985-07-20', NULL, 1190.00);

-- Password: admin123
INSERT INTO usuaris (nom, email, password_hash, rol, actiu) VALUES
('Administrador', 'admin@agencia.test', '$2y$12$FzWlR88FgDltqv.4.HgPkuy6La2ZDcR9.8JAxuyjYgbJ7yPelfWiS', 'ADMIN', 1);
