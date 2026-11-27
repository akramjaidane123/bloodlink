<?php

class Category
{
    private $id;
    private $name;

    public function __construct($name = '', $id = null)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }

    public function setId($id) { $this->id = $id; }
    public function setName($name) { $this->name = $name; }

    public static function fromArray(array $d)
    {
        $objet = new self();
        $objet->setId(isset($d['id']) && $d['id'] !== '' ? (int) $d['id'] : null);
        $objet->setName(trim((string) ($d['name'] ?? '')));
        return $objet;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
