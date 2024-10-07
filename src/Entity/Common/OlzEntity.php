<?php

namespace Olz\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Roles\Role;
use Olz\Entity\Users\User;
use PhpTypeScriptApi\Fields\FieldTypes;

#[ORM\MappedSuperclass]
class OlzEntity {
    #[ORM\Column(type: 'integer', nullable: false, options: ['default' => 1])]
    protected int $on_off;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_user_id', referencedColumnName: 'id', nullable: true)]
    protected ?User $owner_user;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(name: 'owner_role_id', referencedColumnName: 'id', nullable: true)]
    protected ?Role $owner_role;

    #[ORM\Column(type: 'datetime', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected \DateTime $created_at;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by_user_id', referencedColumnName: 'id', nullable: true)]
    protected ?User $created_by_user;

    #[ORM\Column(type: 'datetime', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected \DateTime $last_modified_at;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'last_modified_by_user_id', referencedColumnName: 'id', nullable: true)]
    protected ?User $last_modified_by_user;

    public function getOnOff(): int {
        return $this->on_off;
    }

    public function setOnOff(int $new_value): void {
        $this->on_off = $new_value;
    }

    public function getOwnerUser(): ?User {
        return $this->owner_user;
    }

    public function setOwnerUser(?User $new_value): void {
        $this->owner_user = $new_value;
    }

    public function getOwnerRole(): ?Role {
        return $this->owner_role;
    }

    public function setOwnerRole(?Role $new_value): void {
        $this->owner_role = $new_value;
    }

    public function getCreatedAt(): \DateTime {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $new_value): void {
        $this->created_at = $new_value;
    }

    public function getCreatedByUser(): ?User {
        return $this->created_by_user;
    }

    public function setCreatedByUser(?User $new_value): void {
        $this->created_by_user = $new_value;
    }

    public function getLastModifiedAt(): \DateTime {
        return $this->last_modified_at;
    }

    public function setLastModifiedAt(\DateTime $new_value): void {
        $this->last_modified_at = $new_value;
    }

    public function getLastModifiedByUser(): ?User {
        return $this->last_modified_by_user;
    }

    public function setLastModifiedByUser(?User $new_value): void {
        $this->last_modified_by_user = $new_value;
    }

    /** @return array{ownerUserId: ?int, ownerRoleId: ?int, onOff: bool} */
    public function getMetaData(): array {
        $owner_user = $this->getOwnerUser();
        $owner_role = $this->getOwnerRole();
        return [
            'ownerUserId' => $owner_user ? $owner_user->getId() : null,
            'ownerRoleId' => $owner_role ? $owner_role->getId() : null,
            'onOff' => $this->getOnOff() ? true : false,
        ];
    }

    public static function getMetaField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzMetaDataOrNull' : 'OlzMetaData',
            'field_structure' => [
                'ownerUserId' => new FieldTypes\IntegerField(['allow_null' => true]),
                'ownerRoleId' => new FieldTypes\IntegerField(['allow_null' => true]),
                'onOff' => new FieldTypes\BooleanField(['default_value' => true]),
            ],
            'allow_null' => $allow_null,
        ]);
    }
}
