<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Repository\AccessTokenRepository;

/**
 * @ORM\Entity(repositoryClass=AccessTokenRepository::class)
 * @ORM\Table(
 *     name="access_tokens",
 *     indexes={
 *         @ORM\Index(name="token_index", columns={"token"}),
 *         @ORM\Index(name="user_id_index", columns={"user_id"}),
 *     },
 * )
 */
class AccessToken {
    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $purpose;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $token;
    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expires_at;
    /**
     * @ORM\Id @ORM\Column(type="bigint", nullable=false) @ORM\GeneratedValue
     */
    private $id;

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($new_user) {
        $this->user = $new_user;
    }

    public function getPurpose() {
        return $this->purpose;
    }

    public function setPurpose($new_purpose) {
        $this->purpose = $new_purpose;
    }

    public function getToken() {
        return $this->token;
    }

    public function setToken($new_token) {
        $this->token = $new_token;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($new_created_at) {
        $this->created_at = $new_created_at;
    }

    public function getExpiresAt() {
        return $this->expires_at;
    }

    public function setExpiresAt($new_expires_at) {
        $this->expires_at = $new_expires_at;
    }
}
