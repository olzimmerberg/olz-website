<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Repository\FacebookLinkRepository;

#[ORM\Table(name: 'facebook_links')]
#[ORM\Index(name: 'user_id_index', columns: ['user_id'])]
#[ORM\Entity(repositoryClass: FacebookLinkRepository::class)]
class FacebookLink {
    #[ORM\Column(type: 'text', nullable: false)]
    private $access_token;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private $expires_at;

    #[ORM\Column(type: 'text', nullable: false)]
    private $refresh_token;

    #[ORM\ManyToOne(targetEntity: 'User')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private $user;

    #[ORM\Column(type: 'text', nullable: false)]
    private $facebook_user;

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

    public function getAccessToken() {
        return $this->access_token;
    }

    public function setAccessToken($new_access_token) {
        $this->access_token = $new_access_token;
    }

    public function getExpiresAt() {
        return $this->expires_at;
    }

    public function setExpiresAt($new_expires_at) {
        $this->expires_at = $new_expires_at;
    }

    public function getRefreshToken() {
        return $this->refresh_token;
    }

    public function setRefreshToken($new_refresh_token) {
        $this->refresh_token = $new_refresh_token;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($new_user) {
        $this->user = $new_user;
    }

    public function getFacebookUser() {
        return $this->facebook_user;
    }

    public function setFacebookUser($new_facebook_user) {
        $this->facebook_user = $new_facebook_user;
    }
}
