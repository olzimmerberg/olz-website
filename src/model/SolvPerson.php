<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="solv_people")
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
}
