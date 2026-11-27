<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/Notification.php';

class NotificationC
{
    public function addNotification($notification)
    {
        $sql = "INSERT INTO notifications (user_id, title, message)
                VALUES (:user_id, :title, :message)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'user_id' => $notification->getUserId(),
                'title' => $notification->getTitle(),
                'message' => $notification->getMessage(),
            ]);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function listeNotifications()
    {
        $sql = "SELECT n.*
                FROM notifications n
                ORDER BY n.created_at DESC, n.id DESC";
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

    public function getNotification($id)
    {
        $sql = "SELECT * FROM notifications WHERE id = :id";
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

    public function updateNotification($notification, $id)
    {
        $sql = "UPDATE notifications SET title = :title, message = :message WHERE id = :id";
        $db = config::getConnexion();
        try {
            $params = [
                'title' => $notification->getTitle(),
                'message' => $notification->getMessage(),
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

    public function deleteNotification($id)
    {
        $sql = "DELETE FROM notifications WHERE id = :id";
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

    public function valider($notification)
    {
        $c = $notification;
        $erreurs = [];

        if ($c->getTitle() === '') {
            $erreurs[] = "Titre est obligatoire.";
        } elseif (mb_strlen($c->getTitle()) > 150) {
            $erreurs[] = "Titre : 150 caractères maximum.";
        }

        if ($c->getMessage() === '') {
            $erreurs[] = "Message est obligatoire.";
        } elseif (mb_strlen($c->getMessage()) > 5000) {
            $erreurs[] = "Message : 5000 caractères maximum.";
        }
        return $erreurs;
    }

    public function notificationsPour($userId, $limite = 10)
    {
        $sql = "SELECT * FROM notifications
                WHERE user_id IS NULL OR user_id = :u
                ORDER BY created_at DESC, id DESC
                LIMIT " . (int) $limite;
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['u' => $userId]);
            return $query->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}
