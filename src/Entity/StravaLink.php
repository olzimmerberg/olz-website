<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Repository\StravaLinkRepository;

#[ORM\Table(name: 'strava_links')]
#[ORM\Index(name: 'user_id_index', columns: ['user_id'])]
#[ORM\Entity(repositoryClass: StravaLinkRepository::class)]
class StravaLink {
    #[ORM\Column(type: 'text', nullable: false)]
    private string $access_token;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $expires_at;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $refresh_token;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $strava_user;

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

    public function getAccessToken(): string {
        return $this->access_token;
    }

    public function setAccessToken(string $new_access_token): void {
        $this->access_token = $new_access_token;
    }

    public function getExpiresAt(): \DateTime {
        return $this->expires_at;
    }

    public function setExpiresAt(\DateTime $new_expires_at): void {
        $this->expires_at = $new_expires_at;
    }

    public function getRefreshToken(): string {
        return $this->refresh_token;
    }

    public function setRefreshToken(string $new_refresh_token): void {
        $this->refresh_token = $new_refresh_token;
    }

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $new_user): void {
        $this->user = $new_user;
    }

    public function getStravaUser(): string {
        return $this->strava_user;
    }

    public function setStravaUser(string $new_strava_user): void {
        $this->strava_user = $new_strava_user;
    }
}
