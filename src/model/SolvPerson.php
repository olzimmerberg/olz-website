<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="solv_people",
 * )
 */
class solv_people {
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
    // PRIMARY KEY (`id`)

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
}
