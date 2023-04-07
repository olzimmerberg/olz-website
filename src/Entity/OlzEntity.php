<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use PhpTypeScriptApi\Fields\FieldTypes;

#[ORM\MappedSuperclass]
class OlzEntity {
    #[ORM\Column(type: 'integer', options: ['default' => 1])]
    protected $on_off;

    #[ORM\ManyToOne(targetEntity: '\Olz\Entity\User')]
    #[ORM\JoinColumn(name: 'owner_user_id', referencedColumnName: 'id', nullable: true)]
    protected $owner_user;

    #[ORM\ManyToOne(targetEntity: '\Olz\Entity\Role')]
    #[ORM\JoinColumn(name: 'owner_role_id', referencedColumnName: 'id', nullable: true)]
    protected $owner_role;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected $created_at;

    #[ORM\ManyToOne(targetEntity: '\Olz\Entity\User')]
    #[ORM\JoinColumn(name: 'created_by_user_id', referencedColumnName: 'id', nullable: true)]
    protected $created_by_user;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected $last_modified_at;

    #[ORM\ManyToOne(targetEntity: '\Olz\Entity\User')]
    #[ORM\JoinColumn(name: 'last_modified_by_user_id', referencedColumnName: 'id', nullable: true)]
    protected $last_modified_by_user;

    public function getOnOff() {
        return $this->on_off;
    }

    public function setOnOff($new_on_off) {
        $this->on_off = $new_on_off;
    }

    public function getOwnerUser() {
        return $this->owner_user;
    }

    public function setOwnerUser($new_owner_user) {
        $this->owner_user = $new_owner_user;
    }

    public function getOwnerRole() {
        return $this->owner_role;
    }

    public function setOwnerRole($new_owner_role) {
        $this->owner_role = $new_owner_role;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($new_created_at) {
        $this->created_at = $new_created_at;
    }

    public function getCreatedByUser() {
        return $this->created_by_user;
    }

    public function setCreatedByUser($new_created_by_user) {
        $this->created_by_user = $new_created_by_user;
    }

    public function getLastModifiedAt() {
        return $this->last_modified_at;
    }

    public function setLastModifiedAt($new_last_modified_at) {
        $this->last_modified_at = $new_last_modified_at;
    }

    public function getLastModifiedByUser() {
        return $this->last_modified_by_user;
    }

    public function setLastModifiedByUser($new_last_modified_by_user) {
        $this->last_modified_by_user = $new_last_modified_by_user;
    }

    public static function getMetaField(bool $allow_null) {
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
