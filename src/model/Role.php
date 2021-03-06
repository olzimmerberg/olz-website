<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="RoleRepository")
 * @ORM\Table(
 *     name="roles",
 * )
 */
class Role {
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
    public $name;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $description;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $page;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public $parent_role;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public $index_within_parent;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public $featured_index;
    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    public $can_have_child_roles;
    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
     */
    private $users;

    public function __construct() {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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

    public function getName() {
        return $this->name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getPage() {
        return $this->page;
    }

    public function getParentRoleId() {
        return $this->parent_role;
    }

    public function getUsers() {
        return $this->users;
    }
}
