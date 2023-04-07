<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Repository\RoleRepository;

#[ORM\Table(name: 'roles')]
#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    public $id;

    #[ORM\Column(type: 'text', nullable: false)]
    public $username;

    #[ORM\Column(type: 'text', nullable: true)]
    public $old_username;

    #[ORM\Column(type: 'text', nullable: false)]
    public $name;

    #[ORM\Column(type: 'text', nullable: false, options: ['comment' => 'public'])]
    public $description;

    #[ORM\Column(type: 'text', nullable: false)]
    public $permissions;

    #[ORM\Column(type: 'text', nullable: false, options: ['comment' => 'restricted access'])]
    public $guide;

    #[ORM\Column(type: 'text', nullable: false)]
    public $page;

    #[ORM\Column(type: 'integer', nullable: true)]
    public $parent_role;

    #[ORM\Column(type: 'integer', nullable: true, options: ['comment' => 'negative value: hide role'])]
    public $index_within_parent;

    #[ORM\Column(type: 'integer', nullable: true)]
    public $featured_index;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    public $can_have_child_roles;

    #[ORM\ManyToMany(targetEntity: 'User', mappedBy: 'roles')]
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

    public function setName($new_name) {
        $this->name = $new_name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getPermissions() {
        return $this->permissions;
    }

    public function setPermissions($new_permissions) {
        $this->permissions = $new_permissions;
    }

    public function getGuide() {
        return $this->guide;
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

    public function addUser($new_user) {
        $this->users->add($new_user);
    }
}
