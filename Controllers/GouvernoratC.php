<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/Gouvernorat.php';

class GouvernoratC
{
    public function addGouvernorat($gouvernorat)
    {
        $sql = "INSERT INTO gouvernorats (name)
                VALUES (:name)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'name' => $gouvernorat->getName(),
            ]);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function listeGouvernorats()
    {
        $sql = "SELECT g.*, COUNT(c.id) AS nb_centres
                FROM gouvernorats g
                LEFT JOIN centres c ON c.gouvernorat_id = g.id
                GROUP BY g.id, g.name
                ORDER BY g.name";
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

    public function getGouvernorat($id)
    {
        $sql = "SELECT * FROM gouvernorats WHERE id = :id";
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

    public function updateGouvernorat($gouvernorat, $id)
    {
        $sql = "UPDATE gouvernorats SET name = :name WHERE id = :id";
        $db = config::getConnexion();
        try {
            $params = [
                'name' => $gouvernorat->getName(),
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

    public function deleteGouvernorat($id)
    {
        $sql = "DELETE FROM gouvernorats WHERE id = :id";
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

    public function valider($gouvernorat)
    {
        $c = $gouvernorat;
        $erreurs = [];

        if ($c->getName() === '') {
            $erreurs[] = "Nom est obligatoire.";
        } elseif (mb_strlen($c->getName()) > 80) {
            $erreurs[] = "Nom : 80 caractères maximum.";
        }
        return $erreurs;
    }

    public function options()
    {
        $sql = "SELECT id, name AS label FROM gouvernorats ORDER BY label";
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
