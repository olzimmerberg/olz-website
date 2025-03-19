<?php

namespace Olz\Entity\Roles;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\DataStorageInterface;
use Olz\Entity\Common\DataStorageTrait;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\SearchableInterface;
use Olz\Entity\Users\User;
use Olz\Repository\Roles\RoleRepository;

#[ORM\Table(name: 'roles')]
#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role extends OlzEntity implements DataStorageInterface, SearchableInterface {
    use DataStorageTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    public int $id;

    #[ORM\Column(type: 'text', nullable: false)]
    public string $username;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $old_username;

    #[ORM\Column(type: 'text', nullable: false)]
    public string $name;

    #[ORM\Column(type: 'text', nullable: false, options: ['comment' => 'public'])]
    public string $description;

    #[ORM\Column(type: 'text', nullable: false)]
    public string $permissions;

    #[ORM\Column(type: 'text', nullable: false, options: ['comment' => 'restricted access'])]
    public string $guide;

    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $parent_role;

    #[ORM\Column(type: 'integer', nullable: true, options: ['comment' => 'negative value: hide role'])]
    public ?int $index_within_parent;

    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $featured_index;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    public bool $can_have_child_roles;

    /** @var Collection<int|string, User>&iterable<User> */
    #[ORM\JoinTable(name: 'users_roles')]
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'roles')]
    private Collection $users;

    public function __construct() {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(int $new_id): void {
        $this->id = $new_id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function setUsername(string $new_username): void {
        $this->username = $new_username;
    }

    public function getOldUsername(): ?string {
        return $this->old_username;
    }

    public function setOldUsername(?string $new_old_username): void {
        $this->old_username = $new_old_username;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $new_name): void {
        $this->name = $new_name;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $new_value): void {
        $this->description = $new_value;
    }

    public function getGuide(): string {
        return $this->guide;
    }

    public function setGuide(string $new_value): void {
        $this->guide = $new_value;
    }

    public function getPermissions(): string {
        return $this->permissions;
    }

    public function setPermissions(string $new_value): void {
        $this->permissions = $new_value;
    }

    public function getParentRoleId(): ?int {
        return $this->parent_role;
    }

    public function setParentRoleId(?int $new_value): void {
        $this->parent_role = $new_value;
    }

    /** @return Collection<int|string, User>&iterable<User> */
    public function getUsers(): Collection {
        return $this->users;
    }

    public function addUser(User $user): void {
        $this->users->add($user);
    }

    public function removeUser(User $user): void {
        $this->users->removeElement($user);
    }

    public function getIndexWithinParent(): ?int {
        return $this->index_within_parent;
    }

    public function setIndexWithinParent(?int $new_value): void {
        $this->index_within_parent = $new_value;
    }

    public function getFeaturedIndex(): ?int {
        return $this->featured_index;
    }

    public function setFeaturedIndex(?int $new_value): void {
        $this->featured_index = $new_value;
    }

    public function getCanHaveChildRoles(): bool {
        return $this->can_have_child_roles;
    }

    public function setCanHaveChildRoles(bool $new_value): void {
        $this->can_have_child_roles = $new_value;
    }

    // ---

    public function __toString() {
        $username = $this->getUsername();
        $id = $this->getId();
        return "{$username} (Role ID: {$id})";
    }

    public static function getIdFieldNameForSearch(): string {
        return 'id';
    }

    public function getIdForSearch(): int {
        return $this->getId() ?? 0;
    }

    public static function getCriteriaForQuery(string $query): Expression {
        return Criteria::expr()->orX(
            Criteria::expr()->contains('name', $query),
            Criteria::expr()->contains('title', $query),
            Criteria::expr()->contains('username', $query),
        );
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
