<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\TestableInterface;
use Olz\Repository\AuthRequestRepository;

#[ORM\Table(name: 'auth_requests')]
#[ORM\Index(name: 'ip_address_timestamp_index', columns: ['ip_address', 'timestamp'])]
#[ORM\Entity(repositoryClass: AuthRequestRepository::class)]
class AuthRequest implements TestableInterface {
    #[ORM\Column(type: 'string', length: 40, nullable: false)]
    public string $ip_address;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTime $timestamp;

    #[ORM\Column(type: 'string', length: 31, nullable: false)]
    public string $action;

    #[ORM\Column(type: 'text', nullable: false)]
    public string $username;

    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private int|string $id;

    public function getId(): ?int {
        return isset($this->id) ? intval($this->id) : null;
    }

    public function setId(int $new_id): void {
        $this->id = $new_id;
    }

    public function getIpAddress(): string {
        return $this->ip_address;
    }

    public function setIpAddress(string $new_ip_address): void {
        $this->ip_address = $new_ip_address;
    }

    public function getTimestamp(): ?\DateTime {
        return $this->timestamp;
    }

    public function setTimestamp(?\DateTime $new_timestamp): void {
        $this->timestamp = $new_timestamp;
    }

    public function getAction(): string {
        return $this->action;
    }

    public function setAction(string $new_action): void {
        $this->action = $new_action;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function setUsername(string $new_username): void {
        $this->username = $new_username;
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
