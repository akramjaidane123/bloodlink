<?php

class PasswordReset
{
    private $id;
    private $userId;
    private $channel;
    private $codeHash;
    private $attempts;
    private $expiresAt;
    private $createdAt;

    public function __construct($userId = null, $channel = 'email', $codeHash = '', $expiresAt = '', $id = null)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->channel = $channel;
        $this->codeHash = $codeHash;
        $this->attempts = 0;
        $this->expiresAt = $expiresAt;
        $this->createdAt = '';
    }

    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function getChannel() { return $this->channel; }
    public function getCodeHash() { return $this->codeHash; }
    public function getAttempts() { return $this->attempts; }
    public function getExpiresAt() { return $this->expiresAt; }
    public function getCreatedAt() { return $this->createdAt; }

    public function setId($id) { $this->id = $id; }
    public function setUserId($userId) { $this->userId = $userId; }
    public function setChannel($channel) { $this->channel = $channel; }
    public function setCodeHash($codeHash) { $this->codeHash = $codeHash; }
    public function setAttempts($attempts) { $this->attempts = $attempts; }
    public function setExpiresAt($expiresAt) { $this->expiresAt = $expiresAt; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
}
