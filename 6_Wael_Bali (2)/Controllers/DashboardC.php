<?php
require_once __DIR__ . '/../config.php';

class DashboardC
{
    private function lignes($sql, $params = [])
    {
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

    private function valeur($sql, $params = [])
    {
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute($params);
            return (int) $query->fetchColumn();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    public function statistiques($estAdmin, $scope)
    {
        $w  = $scope !== null ? ' WHERE centre_id = :c' : '';
        $wa = $scope !== null ? ' AND centre_id = :c' : '';
        $p  = $scope !== null ? ['c' => $scope] : [];

        $stats = [
            ['Donneurs inscrits', $this->valeur("SELECT COUNT(*) FROM users WHERE role = 'user'"), 'accent'],
            ['Besoins publiés', $this->valeur('SELECT COUNT(*) FROM needs' . $w, $p), 'red'],
            ['Campagnes', $this->valeur('SELECT COUNT(*) FROM campaigns' . $w, $p), ''],
            ['Réservations en attente', $this->valeur("SELECT COUNT(*) FROM reservations WHERE status = 'pending'" . $wa, $p), 'red'],
        ];
        if ($estAdmin) {
            $stats[] = ['Centres validés', $this->valeur("SELECT COUNT(*) FROM centres WHERE status = 'validated'"), 'accent'];
            $stats[] = ['Articles', $this->valeur('SELECT COUNT(*) FROM articles'), ''];
        }
        return $stats;
    }

    public function reservationsParStatut($scope)
    {
        $w = $scope !== null ? ' WHERE centre_id = :c' : '';
        return $this->lignes('SELECT status, COUNT(*) AS total FROM reservations' . $w . ' GROUP BY status',
            $scope !== null ? ['c' => $scope] : []);
    }

    public function besoinsParGroupe($scope)
    {
        $w = $scope !== null ? ' WHERE centre_id = :c' : '';
        return $this->lignes('SELECT blood_group, SUM(quantity) AS total FROM needs' . $w . ' GROUP BY blood_group ORDER BY total DESC',
            $scope !== null ? ['c' => $scope] : []);
    }

    public function dernieresReservations($scope, $limite = 5)
    {
        $sql = 'SELECT r.id, r.donation_date, r.status, u.username AS donor, c.name AS centre
                FROM reservations r
                INNER JOIN users u ON u.id = r.user_id
                INNER JOIN centres c ON c.id = r.centre_id'
            . ($scope !== null ? ' WHERE r.centre_id = :c' : '')
            . ' ORDER BY r.created_at DESC, r.id DESC LIMIT ' . (int) $limite;
        return $this->lignes($sql, $scope !== null ? ['c' => $scope] : []);
    }

    public function centresEnAttente()
    {
        return $this->lignes("SELECT id, name FROM centres WHERE status = 'pending' ORDER BY name");
    }
}
