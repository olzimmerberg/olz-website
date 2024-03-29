<?php

namespace Olz\Entity\Roles;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\DataStorageInterface;
use Olz\Entity\Common\DataStorageTrait;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\SearchableInterface;
use Olz\Entity\User;
use Olz\Repository\Roles\RoleRepository;

#[ORM\Table(name: 'roles')]
#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role extends OlzEntity implements DataStorageInterface, SearchableInterface {
    use DataStorageTrait;

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

    #[ORM\Column(type: 'text', nullable: true, options: ['comment' => 'page title for SEO'])]
    public $title;

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

    #[ORM\JoinTable(name: 'users_roles')]
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'roles')]
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

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($new_value) {
        $this->title = $new_value;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($new_value) {
        $this->description = $new_value;
    }

    public function getGuide() {
        return $this->guide;
    }

    public function setGuide($new_value) {
        $this->guide = $new_value;
    }

    public function getPage() {
        return $this->page;
    }

    public function getPermissions() {
        return $this->permissions;
    }

    public function setPermissions($new_permissions) {
        $this->permissions = $new_permissions;
    }

    public function getParentRoleId() {
        return $this->parent_role;
    }

    public function setParentRoleId($new_value) {
        $this->parent_role = $new_value;
    }

    public function getUsers() {
        return $this->users;
    }

    public function addUser($new_user) {
        $this->users->add($new_user);
    }

    public function getIndexWithinParent() {
        return $this->index_within_parent;
    }

    public function setIndexWithinParent($new_value) {
        $this->index_within_parent = $new_value;
    }

    public function getFeaturedIndex() {
        return $this->featured_index;
    }

    public function setFeaturedIndex($new_value) {
        $this->featured_index = $new_value;
    }

    public function getCanHaveChildRoles() {
        return $this->can_have_child_roles;
    }

    public function setCanHaveChildRoles($new_value) {
        $this->can_have_child_roles = $new_value;
    }

    // ---

    public static function getIdFieldNameForSearch(): string {
        return 'id';
    }

    public function getIdForSearch(): int {
        return $this->getId();
    }

    public static function getFieldNamesForSearch(): array {
        return ['name'];
    }

    public function getTitleForSearch(): string {
        return $this->getName();
    }

    public static function getEntityNameForStorage(): string {
        return 'roles';
    }

    public function getEntityIdForStorage(): string {
        return "{$this->getId()}";
    }
}
