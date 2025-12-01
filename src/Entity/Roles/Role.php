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
use Olz\Entity\Common\PositionableInterface;
use Olz\Entity\Common\SearchableInterface;
use Olz\Entity\Common\TestableInterface;
use Olz\Entity\Users\User;
use Olz\Repository\Roles\RoleRepository;

#[ORM\Table(name: 'roles')]
#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role extends OlzEntity implements DataStorageInterface, PositionableInterface, SearchableInterface, TestableInterface {
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

    #[ORM\Column(type: 'smallfloat', nullable: true, options: ['comment' => 'null: hide role'])]
    public ?float $position_within_parent;

    #[ORM\Column(type: 'smallfloat', nullable: true, options: ['comment' => 'null: not featured'])]
    public ?float $featured_position;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    public bool $can_have_child_roles;

    /** @var Collection<int|string, User>&iterable<User> */
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

    /** @deprecated Use `getPermissionMap` instead. */
    public function getPermissions(): string {
        return $this->permissions;
    }

    /** @deprecated Use `setPermissionMap` instead. */
    public function setPermissions(string $new_value): void {
        $this->permissions = $new_value;
    }

    /** @return array<string, true> */
    public function getPermissionMap(): array {
        $permission_list = preg_split('/[ ]+/', $this->permissions ?? '');
        if (!is_array($permission_list)) {
            return [];
        }
        $permission_map = [];
        foreach ($permission_list as $permission) {
            if (strlen($permission) > 0) {
                $permission_map[$permission] = true;
            }
        }
        return $permission_map;
    }

    /** @param array<string, bool> $new_value */
    public function setPermissionMap(array $new_value): void {
        $permission_list = [];
        foreach ($new_value as $key => $value) {
            if ($value) {
                $permission_list[] = $key;
            }
        }
        $this->permissions = ' '.implode(' ', $permission_list).' ';
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

    public function getPositionWithinParent(): ?float {
        return $this->position_within_parent;
    }

    public function setPositionWithinParent(?float $new_value): void {
        $this->position_within_parent = $new_value;
    }

    public function getFeaturedPosition(): ?float {
        return $this->featured_position;
    }

    public function setFeaturedPosition(?float $new_value): void {
        $this->featured_position = $new_value;
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

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }

    public static function getEntityNameForStorage(): string {
        return 'roles';
    }

    public function getEntityIdForStorage(): string {
        return "{$this->getId()}";
    }

    public static function getPositionFieldName(string $field): string {
        switch ($field) {
            case 'positionWithinParent':
                return 'position_within_parent';
            case 'featuredPosition':
                return 'featured_position';
            default: throw new \Exception("No such position field: {$field}");
        }
    }

    public function getPositionForEntityField(string $field): ?float {
        switch ($field) {
            case 'positionWithinParent':
                return $this->getPositionWithinParent();
            case 'featuredPosition':
                return $this->getFeaturedPosition();
            default: throw new \Exception("No such position field: {$field}");
        }
    }

    public static function getIdFieldNameForSearch(): string {
        return 'id';
    }

    public function getIdForSearch(): int {
        return $this->getId() ?? 0;
    }

    public function getTitleForSearch(): string {
        return $this->getName();
    }

    public static function getCriteriaForFilter(string $key, string $value): Expression {
        switch ($key) {
            case 'parentRoleId':
                return Criteria::expr()->eq('parent_role', intval($value) ?: null);
            case 'featuredPositionNotNull':
                return Criteria::expr()->isNotNull('featured_position');
            default: throw new \Exception("No such Role filter: {$key}");
        }
    }

    public static function getCriteriaForQuery(string $query): Expression {
        return Criteria::expr()->orX(
            Criteria::expr()->contains('name', $query),
            Criteria::expr()->contains('username', $query),
        );
    }
}
