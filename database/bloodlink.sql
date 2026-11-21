-- BloodLink : base de données (équipe Nexora, 2A11)
-- Import : phpMyAdmin > Importer, ou  mysql -u root < database/bloodlink.sql
-- Si vous aviez importé une ancienne version, supprimez d'abord la base "bloodlink".
CREATE DATABASE IF NOT EXISTS bloodlink CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bloodlink;

-- ===== Module 2 : Centres et gouvernorats (Centre N --- 1 Gouvernorat) =====
CREATE TABLE IF NOT EXISTS gouvernorats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS centres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    address VARCHAR(255) NULL,
    phone VARCHAR(30) NULL,
    status ENUM('pending', 'validated') NOT NULL DEFAULT 'pending',
    gouvernorat_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_centres_gouvernorat FOREIGN KEY (gouvernorat_id) REFERENCES gouvernorats(id) ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ===== Table commune : comptes (user = donneur, centre = personnel d'un centre, admin) =====
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'centre', 'admin') NOT NULL DEFAULT 'user',
    centre_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_centre FOREIGN KEY (centre_id) REFERENCES centres(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ===== Module 3 : Sponsors et contrats (Sponsor 1 --- 1 Contrat) =====
CREATE TABLE IF NOT EXISTS sponsors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    email VARCHAR(150) NULL,
    phone VARCHAR(30) NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(40) NOT NULL UNIQUE,
    sponsor_id INT NOT NULL UNIQUE,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('pending', 'active', 'expired') NOT NULL DEFAULT 'pending',
    CONSTRAINT fk_contracts_sponsor FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===== Module 1 : Campagnes et sponsors (Campagne N --- 1 Sponsor) =====
CREATE TABLE IF NOT EXISTS campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL UNIQUE,
    description TEXT NULL,
    location VARCHAR(150) NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    centre_id INT NOT NULL,
    sponsor_id INT NULL,
    CONSTRAINT fk_campaigns_centre FOREIGN KEY (centre_id) REFERENCES centres(id) ON DELETE CASCADE,
    CONSTRAINT fk_campaigns_sponsor FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Besoins en sang publiés par les centres (par groupe sanguin et niveau d'urgence)
CREATE TABLE IF NOT EXISTS needs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    centre_id INT NOT NULL,
    blood_group ENUM('O+','O-','A+','A-','B+','B-','AB+','AB-') NOT NULL,
    urgency ENUM('low', 'medium', 'urgent') NOT NULL DEFAULT 'medium',
    quantity INT NOT NULL DEFAULT 0,
    UNIQUE KEY uq_need (centre_id, blood_group),
    CONSTRAINT fk_needs_centre FOREIGN KEY (centre_id) REFERENCES centres(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===== Module 6 : Notifications et actualités (Article N --- 1 Catégorie) =====
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ===== Module 4 : Articles et commentaires (Article 1 --- N Commentaire) =====
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    category_id INT NOT NULL,
    author_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_articles_category FOREIGN KEY (category_id) REFERENCES categories(id),
    CONSTRAINT fk_articles_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    status ENUM('visible', 'hidden') NOT NULL DEFAULT 'visible',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comments_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===== Module 5 : Centres et réservations (Centre 1 --- N Réservation) =====
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    centre_id INT NOT NULL,
    donation_date DATE NOT NULL,
    status ENUM('pending','confirmed','refused','cancelled','done') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_resa_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_resa_centre FOREIGN KEY (centre_id) REFERENCES centres(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Alertes envoyées aux donneurs (user_id NULL = alerte pour tous)
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===================== Données de démonstration =====================
INSERT IGNORE INTO gouvernorats (id, name) VALUES (1,'Tunis'),(2,'Sfax'),(3,'Sousse'),(4,'Ariana'),(5,'Nabeul');
INSERT IGNORE INTO centres (id, name, address, phone, status, gouvernorat_id) VALUES
 (1,'Hôpital Charles Nicolle (CRTS Tunis)','Bd 9 Avril, Tunis','71 000 000','validated',1),
 (2,'Centre Régional de Transfusion Sfax','Route de Tunis, Sfax','74 000 000','validated',2),
 (3,'CRTS Sousse','Hôpital Farhat Hached, Sousse','73 000 000','validated',3),
 (4,'Centre Ariana (en attente)','Ariana Ville','71 111 111','pending',4);

-- Comptes démo :  admin@bloodlink.tn / Admin@1234 · centre@bloodlink.tn / Centre@1234 · donneur@bloodlink.tn / Donneur@1234
-- (à changer avant toute mise en ligne)
INSERT IGNORE INTO users (id, username, email, password, role, centre_id) VALUES
 (1,'admin','admin@bloodlink.tn','$2y$10$FnsM18aXtXcdQ9CaJHFu5eDOIU5Kx7HwjEHBO7MoXTclXW0F44.3y','admin',NULL),
 (2,'centre_tunis','centre@bloodlink.tn','$2y$10$0cUP.v.VgMJqYvU0I9UuSOAwqe9LJyfQWupvSw.2NPc6r7By6ZupK','centre',1),
 (3,'donneur','donneur@bloodlink.tn','$2y$10$r.F8KovIQBFE5SGtYiawhe1D.EY0Ssk/pRcZSnfIIFkw7uCSwza1q','user',NULL);

INSERT IGNORE INTO sponsors (id, name, email, phone) VALUES
 (1,'Banque Horizon','contact@horizon.tn','70 100 100'),(2,'Pharma Santé','info@pharmasante.tn','71 200 200');
INSERT IGNORE INTO contracts (id, reference, sponsor_id, start_date, end_date, amount, status) VALUES
 (1,'CT-2026-001',1,'2026-01-01','2026-12-31',5000,'active'),(2,'CT-2026-002',2,'2026-03-01','2027-02-28',3000,'active');
INSERT IGNORE INTO campaigns (id, title, description, location, start_date, end_date, centre_id, sponsor_id) VALUES
 (1,'Collecte Centre-Ville','Grande collecte en plein centre de Tunis.','Avenue Habib Bourguiba, Tunis',DATE_ADD(CURDATE(),INTERVAL 7 DAY),DATE_ADD(CURDATE(),INTERVAL 8 DAY),1,1),
 (2,'Collecte Universitaire','Collecte sur le campus, ouverte aux étudiants.','Campus Ariana',DATE_ADD(CURDATE(),INTERVAL 14 DAY),DATE_ADD(CURDATE(),INTERVAL 15 DAY),1,2),
 (3,'Don du sang Sfax','Journée de mobilisation régionale.','CRTS Sfax',DATE_ADD(CURDATE(),INTERVAL 21 DAY),DATE_ADD(CURDATE(),INTERVAL 21 DAY),2,NULL);
INSERT IGNORE INTO needs (id, centre_id, blood_group, urgency, quantity) VALUES
 (1,1,'O-','urgent',40),(2,1,'A+','medium',25),(3,2,'B+','low',10),(4,3,'A+','medium',20),(5,3,'AB-','urgent',12),(6,2,'O+','medium',30);
INSERT IGNORE INTO categories (id, name) VALUES (1,'Sensibilisation'),(2,'Actualités des centres'),(3,'Conseils santé');
INSERT IGNORE INTO articles (id, title, content, category_id, author_id) VALUES
 (1,'Pourquoi donner son sang ?','Un seul don peut sauver jusqu''à trois vies. Les besoins sont constants : chirurgie, accidents, cancers, maternité.',1,1),
 (2,'Se préparer avant un don','Hydratez-vous, mangez normalement et munissez-vous d''une pièce d''identité avant de vous rendre au centre.',3,1);
INSERT IGNORE INTO comments (id, article_id, user_id, content, status) VALUES (1,1,3,'Merci pour cet article, très utile !','visible');
INSERT IGNORE INTO reservations (id, user_id, centre_id, donation_date, status) VALUES
 (1,3,1,DATE_ADD(CURDATE(),INTERVAL 5 DAY),'pending'),(2,3,2,DATE_ADD(CURDATE(),INTERVAL 12 DAY),'confirmed');
INSERT IGNORE INTO notifications (id, user_id, title, message) VALUES
 (1,NULL,'Besoin urgent en O-','Le CRTS Tunis a un besoin urgent en groupe O-. Réservez votre don !');
