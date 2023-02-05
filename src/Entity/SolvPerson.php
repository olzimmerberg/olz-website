<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Repository\SolvPersonRepository;

/**
 * @ORM\Entity(repositoryClass=SolvPersonRepository::class)
 *
 * @ORM\Table(
 *     name="solv_people",
 * )
 */
class SolvPerson {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $same_as;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $name;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $birth_year;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $domicile;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $member;

    private $valid_field_names = [
        'id' => true,
        'same_as' => true,
        'name' => true,
        'birth_year' => true,
        'domicile' => true,
        'member' => true,
    ];
    // PRIMARY KEY (`id`)

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getSameAs() {
        return $this->same_as;
    }

    public function setSameAs($new_same_as) {
        $this->same_as = $new_same_as;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($new_name) {
        $this->name = $new_name;
    }

    public function getBirthYear() {
        return $this->birth_year;
    }

    public function setBirthYear($new_birth_year) {
        $this->birth_year = $new_birth_year;
    }

    public function getDomicile() {
        return $this->domicile;
    }

    public function setDomicile($new_domicile) {
        $this->domicile = $new_domicile;
    }

    public function getMember() {
        return $this->member;
    }

    public function setMember($new_member) {
        $this->member = $new_member;
    }

    public function getFieldValue($field_name) {
        if (!isset($this->valid_field_names[$field_name])) {
            throw new \Exception("getFieldValue: Invalid field name: {$field_name}", 1);
        }
        return $this->{$field_name};
    }

    public function setFieldValue($field_name, $new_field_value) {
        if (!isset($this->valid_field_names[$field_name])) {
            throw new \Exception("setFieldValue: Invalid field name: {$field_name}", 1);
        }
        $this->{$field_name} = $new_field_value;
    }
}
