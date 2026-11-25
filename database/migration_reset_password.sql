-- Migration : "Réinitialisation du mot de passe (API)"
-- À exécuter UNE SEULE FOIS sur une base bloodlink déjà importée
-- (phpMyAdmin > base bloodlink > onglet SQL). Une base neuve n'en a pas besoin :
-- database/bloodlink.sql contient déjà ces changements.

ALTER TABLE users ADD COLUMN phone CHAR(8) NULL UNIQUE AFTER email;

CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    channel ENUM('email', 'phone') NOT NULL,
    code_hash CHAR(64) NOT NULL,
    attempts TINYINT NOT NULL DEFAULT 0,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_reset_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Numéros de test pour les comptes de démonstration
UPDATE users SET phone = '20000001' WHERE id = 1 AND phone IS NULL;
UPDATE users SET phone = '20000002' WHERE id = 2 AND phone IS NULL;
UPDATE users SET phone = '20000003' WHERE id = 3 AND phone IS NULL;
