<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Users\User;
use Olz\Repository\TelegramLinkRepository;

#[ORM\Table(name: 'telegram_links')]
#[ORM\Index(name: 'pin_index', columns: ['pin'])]
#[ORM\Index(name: 'user_id_index', columns: ['user_id'])]
#[ORM\Index(name: 'telegram_user_id_index', columns: ['telegram_user_id'])]
#[ORM\Index(name: 'telegram_chat_id_index', columns: ['telegram_chat_id'])]
#[ORM\Entity(repositoryClass: TelegramLinkRepository::class)]
class TelegramLink {
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $pin;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $pin_expires_at;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $user;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $telegram_chat_id;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $telegram_user_id;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $telegram_chat_state;

    #[ORM\Column(type: 'datetime', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $created_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $linked_at;

    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private int|string $id;

    public function getId(): ?int {
        return isset($this->id) ? intval($this->id) : null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
    }

    public function getPin(): ?string {
        return $this->pin;
    }

    public function setPin(?string $new_value): void {
        $this->pin = $new_value;
    }

    public function getPinExpiresAt(): ?\DateTime {
        return $this->pin_expires_at;
    }

    public function setPinExpiresAt(?\DateTime $new_value): void {
        $this->pin_expires_at = $new_value;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $new_value): void {
        $this->user = $new_value;
    }

    public function getTelegramChatId(): ?string {
        return $this->telegram_chat_id;
    }

    public function setTelegramChatId(?string $new_value): void {
        $this->telegram_chat_id = $new_value;
    }

    public function getTelegramUserId(): ?string {
        return $this->telegram_user_id;
    }

    public function setTelegramUserId(?string $new_value): void {
        $this->telegram_user_id = $new_value;
    }

    /** @return array<string, mixed> */
    public function getTelegramChatState(): array {
        if ($this->telegram_chat_state == null) {
            return [];
        }
        $array = json_decode($this->telegram_chat_state, true);
        return is_array($array) ? $array : [];
    }

    /** @param array<string, mixed> $new_value */
    public function setTelegramChatState(array $new_value): void {
        $enc_value = json_encode($new_value);
        if (!$enc_value) {
            return;
        }
        $this->telegram_chat_state = $enc_value;
    }

    public function getCreatedAt(): \DateTime {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $new_value): void {
        $this->created_at = $new_value;
    }

    public function getLinkedAt(): ?\DateTime {
        return $this->linked_at;
    }

    public function setLinkedAt(?\DateTime $new_value): void {
        $this->linked_at = $new_value;
    }
}
