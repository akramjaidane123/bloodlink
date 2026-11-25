<?php

class Campaign
{
    private $id;
    private $title;
    private $description;
    private $location;
    private $startDate;
    private $endDate;
    private $centreId;
    private $sponsorId;

    public function __construct($title = '', $description = '', $location = '', $startDate = '', $endDate = '', $centreId = null, $sponsorId = null, $id = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->location = $location;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->centreId = $centreId;
        $this->sponsorId = $sponsorId;
    }

    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getDescription() { return $this->description; }
    public function getLocation() { return $this->location; }
    public function getStartDate() { return $this->startDate; }
    public function getEndDate() { return $this->endDate; }
    public function getCentreId() { return $this->centreId; }
    public function getSponsorId() { return $this->sponsorId; }

    public function setId($id) { $this->id = $id; }
    public function setTitle($title) { $this->title = $title; }
    public function setDescription($description) { $this->description = $description; }
    public function setLocation($location) { $this->location = $location; }
    public function setStartDate($startDate) { $this->startDate = $startDate; }
    public function setEndDate($endDate) { $this->endDate = $endDate; }
    public function setCentreId($centreId) { $this->centreId = $centreId; }
    public function setSponsorId($sponsorId) { $this->sponsorId = $sponsorId; }

    public static function fromArray(array $d)
    {
        $objet = new self();
        $objet->setId(isset($d['id']) && $d['id'] !== '' ? (int) $d['id'] : null);
        $objet->setTitle(trim((string) ($d['title'] ?? '')));
        $objet->setDescription(trim((string) ($d['description'] ?? '')));
        $objet->setLocation(trim((string) ($d['location'] ?? '')));
        $objet->setStartDate(trim((string) ($d['start_date'] ?? '')));
        $objet->setEndDate(trim((string) ($d['end_date'] ?? '')));
        $objet->setCentreId(isset($d['centre_id']) && $d['centre_id'] !== '' ? (int) $d['centre_id'] : null);
        $objet->setSponsorId(isset($d['sponsor_id']) && $d['sponsor_id'] !== '' ? (int) $d['sponsor_id'] : null);
        return $objet;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'centre_id' => $this->centreId,
            'sponsor_id' => $this->sponsorId,
        ];
    }
}
