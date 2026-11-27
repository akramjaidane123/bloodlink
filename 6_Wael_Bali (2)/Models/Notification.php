<?php

class Notification
{
    private $id;
    private $userId;
    private $title;
    private $message;

    public function __construct($userId = null, $title = '', $message = '', $id = null)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
        $this->message = $message;
    }

    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function getTitle() { return $this->title; }
    public function getMessage() { return $this->message; }

    public function setId($id) { $this->id = $id; }
    public function setUserId($userId) { $this->userId = $userId; }
    public function setTitle($title) { $this->title = $title; }
    public function setMessage($message) { $this->message = $message; }

    public static function fromArray(array $d)
    {
        $objet = new self();
        $objet->setId(isset($d['id']) && $d['id'] !== '' ? (int) $d['id'] : null);
        $objet->setUserId(isset($d['user_id']) && $d['user_id'] !== '' ? (int) $d['user_id'] : null);
        $objet->setTitle(trim((string) ($d['title'] ?? '')));
        $objet->setMessage(trim((string) ($d['message'] ?? '')));
        return $objet;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'title' => $this->title,
            'message' => $this->message,
        ];
    }
}
