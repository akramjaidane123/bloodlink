<?php

class Centre
{
    private $id;
    private $name;
    private $address;
    private $phone;
    private $status;
    private $gouvernoratId;

    public function __construct($name = '', $address = '', $phone = '', $status = 'pending', $gouvernoratId = null, $id = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->phone = $phone;
        $this->status = $status;
        $this->gouvernoratId = $gouvernoratId;
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getAddress() { return $this->address; }
    public function getPhone() { return $this->phone; }
    public function getStatus() { return $this->status; }
    public function getGouvernoratId() { return $this->gouvernoratId; }

    public function setId($id) { $this->id = $id; }
    public function setName($name) { $this->name = $name; }
    public function setAddress($address) { $this->address = $address; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setStatus($status) { $this->status = $status; }
    public function setGouvernoratId($gouvernoratId) { $this->gouvernoratId = $gouvernoratId; }

    public static function fromArray(array $d)
    {
        $objet = new self();
        $objet->setId(isset($d['id']) && $d['id'] !== '' ? (int) $d['id'] : null);
        $objet->setName(trim((string) ($d['name'] ?? '')));
        $objet->setAddress(trim((string) ($d['address'] ?? '')));
        $objet->setPhone(trim((string) ($d['phone'] ?? '')));
        $objet->setStatus(trim((string) ($d['status'] ?? '')));
        $objet->setGouvernoratId(isset($d['gouvernorat_id']) && $d['gouvernorat_id'] !== '' ? (int) $d['gouvernorat_id'] : null);
        return $objet;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'status' => $this->status,
            'gouvernorat_id' => $this->gouvernoratId,
        ];
    }
}
