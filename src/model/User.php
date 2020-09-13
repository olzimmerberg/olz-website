<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\Table(
 *     name="users",
 * )
 */
class User {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    public $id;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $username;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public $old_username;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $password;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $email;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $first_name;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $last_name;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $zugriff;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public $root;
    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     * @ORM\JoinTable(name="users_roles")
     */
    private $roles;

    public function __construct() {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($new_username) {
        $this->username = $new_username;
    }

    public function getOldUsername() {
        return $this->old_username;
    }

    public function setOldUsername($new_old_username) {
        $this->old_username = $new_old_username;
    }

    public function getPasswordHash() {
        return $this->password;
    }

    public function setPasswordHash($new_password_hash) {
        $this->password = $new_password_hash;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($new_email) {
        $this->email = $new_email;
    }

    public function getFirstName() {
        return $this->first_name;
    }

    public function setFirstName($new_first_name) {
        $this->first_name = $new_first_name;
    }

    public function getLastName() {
        return $this->last_name;
    }

    public function setLastName($new_last_name) {
        $this->last_name = $new_last_name;
    }

    public function getFullName() {
        return "{$this->getFirstName()} {$this->getLastName()}";
    }

    public function getZugriff() {
        return $this->zugriff;
    }

    public function setZugriff($new_zugriff) {
        $this->zugriff = $new_zugriff;
    }

    public function getRoot() {
        return $this->root;
    }

    public function setRoot($new_root) {
        $this->root = $new_root;
    }
}
