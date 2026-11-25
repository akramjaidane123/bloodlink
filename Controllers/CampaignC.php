<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/Campaign.php';

class CampaignC
{
    public function addCampaign($campagne)
    {
        $sql = "INSERT INTO campaigns (title, description, location, start_date, end_date, centre_id, sponsor_id)
                VALUES (:title, :description, :location, :start_date, :end_date, :centre_id, :sponsor_id)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'title' => $campagne->getTitle(),
                'description' => $campagne->getDescription(),
                'location' => $campagne->getLocation(),
                'start_date' => $campagne->getStartDate(),
                'end_date' => $campagne->getEndDate(),
                'centre_id' => $campagne->getCentreId(),
                'sponsor_id' => $campagne->getSponsorId(),
            ]);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function listeCampaigns($scope = null)
    {
        $where = ($scope !== null) ? 'WHERE ca.centre_id = :scope' : '';
        $sql = "SELECT ca.*, c.name AS centre, s.name AS sponsor
                FROM campaigns ca
                INNER JOIN centres c ON c.id = ca.centre_id
                LEFT JOIN sponsors s ON s.id = ca.sponsor_id
                $where
                ORDER BY ca.start_date DESC";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute($scope !== null ? ['scope' => $scope] : []);
            return $query->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function getCampaign($id, $scope = null)
    {
        $sql = "SELECT * FROM campaigns WHERE id = :id" . ($scope !== null ? " AND centre_id = :scope" : '');
        $params = ['id' => $id] + ($scope !== null ? ['scope' => $scope] : []);
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

    public function updateCampaign($campagne, $id, $scope = null)
    {
        $sql = "UPDATE campaigns SET title = :title, description = :description, location = :location, start_date = :start_date, end_date = :end_date, centre_id = :centre_id, sponsor_id = :sponsor_id WHERE id = :id" . ($scope !== null ? " AND centre_id = :scope" : '');
        $db = config::getConnexion();
        try {
            $params = [
                'title' => $campagne->getTitle(),
                'description' => $campagne->getDescription(),
                'location' => $campagne->getLocation(),
                'start_date' => $campagne->getStartDate(),
                'end_date' => $campagne->getEndDate(),
                'centre_id' => $campagne->getCentreId(),
                'sponsor_id' => $campagne->getSponsorId(),
                'id' => $id,
            ];
            if ($scope !== null) {
                $params['scope'] = $scope;
            }
            $query = $db->prepare($sql);
            $query->execute($params);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function deleteCampaign($id, $scope = null)
    {
        $sql = "DELETE FROM campaigns WHERE id = :id" . ($scope !== null ? " AND centre_id = :scope" : '');
        $params = ['id' => $id] + ($scope !== null ? ['scope' => $scope] : []);
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

    public function valider($campagne)
    {
        $c = $campagne;
        $erreurs = [];

        if ($c->getTitle() === '') {
            $erreurs[] = "Titre est obligatoire.";
        } elseif (mb_strlen($c->getTitle()) > 150) {
            $erreurs[] = "Titre : 150 caractères maximum.";
        }

        if (mb_strlen($c->getDescription()) > 5000) {
            $erreurs[] = "Description : 5000 caractères maximum.";
        }

        if (mb_strlen($c->getLocation()) > 150) {
            $erreurs[] = "Lieu : 150 caractères maximum.";
        }

        $d = DateTime::createFromFormat('Y-m-d', (string) $c->getStartDate());
        if (!$d || $d->format('Y-m-d') !== $c->getStartDate()) {
            $erreurs[] = "Date de début : date invalide.";
        }

        $d = DateTime::createFromFormat('Y-m-d', (string) $c->getEndDate());
        if (!$d || $d->format('Y-m-d') !== $c->getEndDate()) {
            $erreurs[] = "Date de fin : date invalide.";
        }

        if (!$c->getCentreId() || $c->getCentreId() <= 0) {
            $erreurs[] = "Centre est obligatoire.";
        }

        if ($c->getSponsorId() !== null && $c->getSponsorId() <= 0) {
            $erreurs[] = "Sponsor : valeur invalide.";
        }
        if ($c->getStartDate() !== '' && $c->getEndDate() !== '' && $c->getStartDate() > $c->getEndDate()) {
            $erreurs[] = 'La date de fin doit être postérieure ou égale à la date de début.';
        }
        return $erreurs;
    }

    public function campagnesAVenir($limite = 50)
    {
        $sql = "SELECT ca.*, c.name AS centre, s.name AS sponsor
                FROM campaigns ca
                INNER JOIN centres c ON c.id = ca.centre_id
                LEFT JOIN sponsors s ON s.id = ca.sponsor_id
                WHERE ca.end_date >= CURDATE()
                ORDER BY ca.start_date
                LIMIT " . (int) $limite;
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
}
