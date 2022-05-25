<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Repository\AuthRequestRepository;

/**
 * @ORM\Entity(repositoryClass=AuthRequestRepository::class)
 * @ORM\Table(
 *     name="auth_requests",
 *     indexes={@ORM\Index(name="ip_address_timestamp_index", columns={"ip_address", "timestamp"})},
 * )
 */
class AuthRequest {
    /**
     * @ORM\Column(type="string", length=40, nullable=false)
     */
    public $ip_address;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $timestamp;
    /**
     * @ORM\Column(type="string", length=31, nullable=false)
     */
    public $action;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $username;
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

    public function getIpAddress() {
        return $this->ip_address;
    }

    public function setIpAddress($new_ip_address) {
        $this->ip_address = $new_ip_address;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }

    public function setTimestamp($new_timestamp) {
        $this->timestamp = $new_timestamp;
    }

    public function getAction() {
        return $this->action;
    }

    public function setAction($new_action) {
        $this->action = $new_action;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($new_username) {
        $this->username = $new_username;
    }
}
