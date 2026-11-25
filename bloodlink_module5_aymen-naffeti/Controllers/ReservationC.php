<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/Reservation.php';

class ReservationC
{
    public function addReservation($reservation)
    {
        $sql = "INSERT INTO reservations (user_id, centre_id, donation_date, status)
                VALUES (:user_id, :centre_id, :donation_date, :status)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'user_id' => $reservation->getUserId(),
                'centre_id' => $reservation->getCentreId(),
                'donation_date' => $reservation->getDonationDate(),
                'status' => $reservation->getStatus(),
            ]);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function listeReservations($scope = null)
    {
        $where = ($scope !== null) ? 'WHERE r.centre_id = :scope' : '';
        $sql = "SELECT r.*, u.username AS donor, c.name AS centre
                FROM reservations r
                INNER JOIN users u ON u.id = r.user_id
                INNER JOIN centres c ON c.id = r.centre_id
                $where
                ORDER BY r.donation_date DESC, r.id DESC";
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

    public function getReservation($id, $scope = null)
    {
        $sql = "SELECT * FROM reservations WHERE id = :id" . ($scope !== null ? " AND centre_id = :scope" : '');
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

    public function updateReservation($reservation, $id, $scope = null)
    {
        $sql = "UPDATE reservations SET donation_date = :donation_date, status = :status WHERE id = :id" . ($scope !== null ? " AND centre_id = :scope" : '');
        $db = config::getConnexion();
        try {
            $params = [
                'donation_date' => $reservation->getDonationDate(),
                'status' => $reservation->getStatus(),
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

    public function deleteReservation($id, $scope = null)
    {
        $sql = "DELETE FROM reservations WHERE id = :id" . ($scope !== null ? " AND centre_id = :scope" : '');
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

    public function valider($reservation)
    {
        $c = $reservation;
        $erreurs = [];

        $d = DateTime::createFromFormat('Y-m-d', (string) $c->getDonationDate());
        if (!$d || $d->format('Y-m-d') !== $c->getDonationDate()) {
            $erreurs[] = "Date du don : date invalide.";
        }

        if (!array_key_exists($c->getStatus(), labels()['reservation'])) {
            $erreurs[] = "Statut : valeur invalide.";
        }
        return $erreurs;
    }

    public function reservationsDuDonneur($userId)
    {
        $sql = "SELECT r.*, c.name AS centre
                FROM reservations r
                INNER JOIN centres c ON c.id = r.centre_id
                WHERE r.user_id = :u
                ORDER BY r.donation_date DESC, r.id DESC";
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

    public function validerDemande($centreId, $date)
    {
        require_once __DIR__ . '/CentreC.php';
        $erreurs = [];
        if (!$centreId || !(new CentreC())->estValide($centreId)) {
            $erreurs[] = 'Veuillez choisir un centre valide.';
        }
        $d = DateTime::createFromFormat('Y-m-d', (string) $date);
        $min = (new DateTime('tomorrow'))->setTime(0, 0);
        $max = new DateTime('+90 days');
        if (!$d || $d->format('Y-m-d') !== $date || $d < $min || $d > $max) {
            $erreurs[] = 'Choisissez une date entre demain et les 90 prochains jours.';
        }
        return $erreurs;
    }

    public function annulerReservation($id, $userId)
    {
        $sql = "UPDATE reservations SET status = 'cancelled'
                WHERE id = :id AND user_id = :u AND status IN ('pending', 'confirmed')";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id, 'u' => $userId]);
            return $query->rowCount() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
