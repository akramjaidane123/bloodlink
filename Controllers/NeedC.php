<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/Need.php';

class NeedC
{
    public function addNeed($besoin)
    {
        $sql = "INSERT INTO needs (centre_id, blood_group, urgency, quantity)
                VALUES (:centre_id, :blood_group, :urgency, :quantity)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'centre_id' => $besoin->getCentreId(),
                'blood_group' => $besoin->getBloodGroup(),
                'urgency' => $besoin->getUrgency(),
                'quantity' => $besoin->getQuantity(),
            ]);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function listeNeeds($scope = null)
    {
        $where = ($scope !== null) ? 'WHERE n.centre_id = :scope' : '';
        $sql = "SELECT n.*, c.name AS centre
                FROM needs n
                INNER JOIN centres c ON c.id = n.centre_id
                $where
                ORDER BY n.id DESC";
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

    public function getNeed($id, $scope = null)
    {
        $sql = "SELECT * FROM needs WHERE id = :id" . ($scope !== null ? " AND centre_id = :scope" : '');
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

    public function updateNeed($besoin, $id, $scope = null)
    {
        $sql = "UPDATE needs SET centre_id = :centre_id, blood_group = :blood_group, urgency = :urgency, quantity = :quantity WHERE id = :id" . ($scope !== null ? " AND centre_id = :scope" : '');
        $db = config::getConnexion();
        try {
            $params = [
                'centre_id' => $besoin->getCentreId(),
                'blood_group' => $besoin->getBloodGroup(),
                'urgency' => $besoin->getUrgency(),
                'quantity' => $besoin->getQuantity(),
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

    public function deleteNeed($id, $scope = null)
    {
        $sql = "DELETE FROM needs WHERE id = :id" . ($scope !== null ? " AND centre_id = :scope" : '');
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

    public function valider($besoin)
    {
        $c = $besoin;
        $erreurs = [];

        if (!$c->getCentreId() || $c->getCentreId() <= 0) {
            $erreurs[] = "Centre est obligatoire.";
        }

        if (!array_key_exists($c->getBloodGroup(), labels()['blood'])) {
            $erreurs[] = "Groupe sanguin : valeur invalide.";
        }

        if (!array_key_exists($c->getUrgency(), labels()['urgency'])) {
            $erreurs[] = "Niveau d'urgence : valeur invalide.";
        }

        if (!ctype_digit((string) $c->getQuantity())) {
            $erreurs[] = "Poches nécessaires : nombre entier positif attendu.";
        }
        return $erreurs;
    }

    public function besoinsFiltres($groupe = null, $gouvernorat = null, $limite = 100)
    {
        $sql = "SELECT n.*, c.name AS centre, g.name AS gouvernorat
                FROM needs n
                INNER JOIN centres c ON c.id = n.centre_id
                INNER JOIN gouvernorats g ON g.id = c.gouvernorat_id
                WHERE c.status = 'validated'";
        $params = [];
        if ($groupe) {
            $sql .= ' AND n.blood_group = :b';
            $params['b'] = $groupe;
        }
        if ($gouvernorat) {
            $sql .= ' AND g.id = :g';
            $params['g'] = $gouvernorat;
        }
        $sql .= " ORDER BY FIELD(n.urgency, 'urgent', 'medium', 'low'), n.quantity DESC LIMIT " . (int) $limite;
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
}
