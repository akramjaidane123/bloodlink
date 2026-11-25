<?php

class Need
{
    private $id;
    private $centreId;
    private $bloodGroup;
    private $urgency;
    private $quantity;

    public function __construct($centreId = null, $bloodGroup = '', $urgency = 'medium', $quantity = '', $id = null)
    {
        $this->id = $id;
        $this->centreId = $centreId;
        $this->bloodGroup = $bloodGroup;
        $this->urgency = $urgency;
        $this->quantity = $quantity;
    }

    public function getId() { return $this->id; }
    public function getCentreId() { return $this->centreId; }
    public function getBloodGroup() { return $this->bloodGroup; }
    public function getUrgency() { return $this->urgency; }
    public function getQuantity() { return $this->quantity; }

    public function setId($id) { $this->id = $id; }
    public function setCentreId($centreId) { $this->centreId = $centreId; }
    public function setBloodGroup($bloodGroup) { $this->bloodGroup = $bloodGroup; }
    public function setUrgency($urgency) { $this->urgency = $urgency; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }

    public static function fromArray(array $d)
    {
        $objet = new self();
        $objet->setId(isset($d['id']) && $d['id'] !== '' ? (int) $d['id'] : null);
        $objet->setCentreId(isset($d['centre_id']) && $d['centre_id'] !== '' ? (int) $d['centre_id'] : null);
        $objet->setBloodGroup(trim((string) ($d['blood_group'] ?? '')));
        $objet->setUrgency(trim((string) ($d['urgency'] ?? '')));
        $objet->setQuantity(trim((string) ($d['quantity'] ?? '')));
        return $objet;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'centre_id' => $this->centreId,
            'blood_group' => $this->bloodGroup,
            'urgency' => $this->urgency,
            'quantity' => $this->quantity,
        ];
    }
}
