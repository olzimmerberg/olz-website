<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\TestableInterface;
use Olz\Entity\Users\User;
use Olz\Repository\AccessTokenRepository;

#[ORM\Table(name: 'access_tokens')]
#[ORM\Index(name: 'token_index', columns: ['token'])]
#[ORM\Index(name: 'user_id_index', columns: ['user_id'])]
#[ORM\Entity(repositoryClass: AccessTokenRepository::class)]
class AccessToken implements TestableInterface {
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $user;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $purpose;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $token;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $created_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $expires_at;

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

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $new_user): void {
        $this->user = $new_user;
    }

    public function getPurpose(): string {
        return $this->purpose;
    }

    public function setPurpose(string $new_purpose): void {
        $this->purpose = $new_purpose;
    }

    public function getToken(): string {
        return $this->token;
    }

    public function setToken(string $new_token): void {
        $this->token = $new_token;
    }

    public function getCreatedAt(): \DateTime {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $new_created_at): void {
        $this->created_at = $new_created_at;
    }

    public function getExpiresAt(): ?\DateTime {
        return $this->expires_at;
    }

    public function setExpiresAt(?\DateTime $new_expires_at): void {
        $this->expires_at = $new_expires_at;
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
