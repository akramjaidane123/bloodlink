<?php

class Reservation
{
    private $id;
    private $userId;
    private $centreId;
    private $donationDate;
    private $status;

    public function __construct($userId = null, $centreId = null, $donationDate = '', $status = 'pending', $id = null)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->centreId = $centreId;
        $this->donationDate = $donationDate;
        $this->status = $status;
    }

    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function getCentreId() { return $this->centreId; }
    public function getDonationDate() { return $this->donationDate; }
    public function getStatus() { return $this->status; }

    public function setId($id) { $this->id = $id; }
    public function setUserId($userId) { $this->userId = $userId; }
    public function setCentreId($centreId) { $this->centreId = $centreId; }
    public function setDonationDate($donationDate) { $this->donationDate = $donationDate; }
    public function setStatus($status) { $this->status = $status; }

    public static function fromArray(array $d)
    {
        $objet = new self();
        $objet->setId(isset($d['id']) && $d['id'] !== '' ? (int) $d['id'] : null);
        $objet->setUserId(isset($d['user_id']) && $d['user_id'] !== '' ? (int) $d['user_id'] : null);
        $objet->setCentreId(isset($d['centre_id']) && $d['centre_id'] !== '' ? (int) $d['centre_id'] : null);
        $objet->setDonationDate(trim((string) ($d['donation_date'] ?? '')));
        $objet->setStatus(trim((string) ($d['status'] ?? '')));
        return $objet;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'centre_id' => $this->centreId,
            'donation_date' => $this->donationDate,
            'status' => $this->status,
        ];
    }
}
