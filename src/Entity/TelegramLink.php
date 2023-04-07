<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Repository\TelegramLinkRepository;

#[ORM\Table(name: 'telegram_links')]
#[ORM\Index(name: 'pin_index', columns: ['pin'])]
#[ORM\Index(name: 'user_id_index', columns: ['user_id'])]
#[ORM\Index(name: 'telegram_user_id_index', columns: ['telegram_user_id'])]
#[ORM\Index(name: 'telegram_chat_id_index', columns: ['telegram_chat_id'])]
#[ORM\Entity(repositoryClass: TelegramLinkRepository::class)]
class TelegramLink {
    #[ORM\Column(type: 'string', nullable: true)]
    private $pin;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $pin_expires_at;

    #[ORM\ManyToOne(targetEntity: 'User')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private $user;

    #[ORM\Column(type: 'string', nullable: true)]
    private $telegram_chat_id;

    #[ORM\Column(type: 'string', nullable: true)]
    private $telegram_user_id;

    #[ORM\Column(type: 'text', nullable: false)]
    private $telegram_chat_state;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private $created_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $linked_at;

    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getPin() {
        return $this->pin;
    }

    public function setPin($new_pin) {
        $this->pin = $new_pin;
    }

    public function getPinExpiresAt() {
        return $this->pin_expires_at;
    }

    public function setPinExpiresAt($new_pin_expires_at) {
        $this->pin_expires_at = $new_pin_expires_at;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($new_user) {
        $this->user = $new_user;
    }

    public function getTelegramChatId() {
        return $this->telegram_chat_id;
    }

    public function setTelegramChatId($new_telegram_chat_id) {
        $this->telegram_chat_id = $new_telegram_chat_id;
    }

    public function getTelegramUserId() {
        return $this->telegram_user_id;
    }

    public function setTelegramUserId($new_telegram_user_id) {
        $this->telegram_user_id = $new_telegram_user_id;
    }

    public function getTelegramChatState() {
        if ($this->telegram_chat_state == null) {
            return [];
        }
        return json_decode($this->telegram_chat_state, true);
    }

    public function setTelegramChatState($new_telegram_chat_state) {
        $this->telegram_chat_state = json_encode($new_telegram_chat_state);
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($new_created_at) {
        $this->created_at = $new_created_at;
    }

    public function getLinkedAt() {
        return $this->linked_at;
    }

    public function setLinkedAt($new_linked_at) {
        $this->linked_at = $new_linked_at;
    }
}
