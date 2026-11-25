<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/Centre.php';

class CentreC
{
    public function addCentre($centre)
    {
        $sql = "INSERT INTO centres (name, address, phone, status, gouvernorat_id)
                VALUES (:name, :address, :phone, :status, :gouvernorat_id)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'name' => $centre->getName(),
                'address' => $centre->getAddress(),
                'phone' => $centre->getPhone(),
                'status' => $centre->getStatus(),
                'gouvernorat_id' => $centre->getGouvernoratId(),
            ]);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function listeCentres()
    {
        $sql = "SELECT c.*, g.name AS gouvernorat
                FROM centres c
                INNER JOIN gouvernorats g ON g.id = c.gouvernorat_id
                ORDER BY c.name";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function getCentre($id)
    {
        $sql = "SELECT * FROM centres WHERE id = :id";
        $params = ['id' => $id];
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute($params);
            $ligne = $query->fetch();
            return $ligne ? $ligne : null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function updateCentre($centre, $id)
    {
        $sql = "UPDATE centres SET name = :name, address = :address, phone = :phone, status = :status, gouvernorat_id = :gouvernorat_id WHERE id = :id";
        $db = config::getConnexion();
        try {
            $params = [
                'name' => $centre->getName(),
                'address' => $centre->getAddress(),
                'phone' => $centre->getPhone(),
                'status' => $centre->getStatus(),
                'gouvernorat_id' => $centre->getGouvernoratId(),
                'id' => $id,
            ];
            $query = $db->prepare($sql);
            $query->execute($params);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function deleteCentre($id)
    {
        $sql = "DELETE FROM centres WHERE id = :id";
        $params = ['id' => $id];
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute($params);
            return $query->rowCount() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function valider($centre)
    {
        $c = $centre;
        $erreurs = [];

        if ($c->getName() === '') {
            $erreurs[] = "Nom est obligatoire.";
        } elseif (mb_strlen($c->getName()) > 120) {
            $erreurs[] = "Nom : 120 caractères maximum.";
        }

        if (mb_strlen($c->getAddress()) > 255) {
            $erreurs[] = "Adresse : 255 caractères maximum.";
        }

        if (mb_strlen($c->getPhone()) > 30) {
            $erreurs[] = "Téléphone : 30 caractères maximum.";
        } elseif ($c->getPhone() !== '' && !preg_match('/^[0-9 +().-]{6,30}$/', $c->getPhone())) {
            $erreurs[] = "Téléphone : numéro de téléphone invalide.";
        }

        if (!array_key_exists($c->getStatus(), labels()['centre'])) {
            $erreurs[] = "Statut : valeur invalide.";
        }

        if (!$c->getGouvernoratId() || $c->getGouvernoratId() <= 0) {
            $erreurs[] = "Gouvernorat est obligatoire.";
        }
        return $erreurs;
    }

    public function options()
    {
        $sql = "SELECT id, name AS label FROM centres ORDER BY label";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function centresValides($gouvernorat = null)
    {
        $sql = "SELECT c.*, g.name AS gouvernorat
                FROM centres c
                INNER JOIN gouvernorats g ON g.id = c.gouvernorat_id
                WHERE c.status = 'validated'";
        $params = [];
        if ($gouvernorat) {
            $sql .= ' AND c.gouvernorat_id = :g';
            $params['g'] = $gouvernorat;
        }
        $sql .= ' ORDER BY g.name, c.name';
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute($params);
            return $query->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function estValide($id)
    {
        $db = config::getConnexion();
        try {
            $query = $db->prepare("SELECT COUNT(*) FROM centres WHERE id = :id AND status = 'validated'");
            $query->execute(['id' => $id]);
            return (int) $query->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
