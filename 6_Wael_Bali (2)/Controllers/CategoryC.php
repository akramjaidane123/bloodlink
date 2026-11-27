<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/Category.php';

class CategoryC
{
    public function addCategory($categorie)
    {
        $sql = "INSERT INTO categories (name)
                VALUES (:name)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'name' => $categorie->getName(),
            ]);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function listeCategories()
    {
        $sql = "SELECT c.*, COUNT(a.id) AS nb_articles
                FROM categories c
                LEFT JOIN articles a ON a.category_id = c.id
                GROUP BY c.id, c.name
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

    public function getCategory($id)
    {
        $sql = "SELECT * FROM categories WHERE id = :id";
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

    public function updateCategory($categorie, $id)
    {
        $sql = "UPDATE categories SET name = :name WHERE id = :id";
        $db = config::getConnexion();
        try {
            $params = [
                'name' => $categorie->getName(),
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

    public function deleteCategory($id)
    {
        $sql = "DELETE FROM categories WHERE id = :id";
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

    public function valider($categorie)
    {
        $c = $categorie;
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
        $sql = "SELECT id, name AS label FROM categories ORDER BY label";
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
