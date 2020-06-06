<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="solv_results",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="person_run_unique", columns={"person", "event", "class", "name", "birth_year", "domicile", "club"})},
 * )
 */
class SolvResult {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $person;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $event;
    /**
     * @ORM\Column(type="string", nullable=false, length=15)
     */
    private $class;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $rank;
    /**
     * @ORM\Column(type="string", nullable=false, length=31)
     */
    private $name;
    /**
     * @ORM\Column(type="string", nullable=false, length=3)
     */
    private $birth_year;
    /**
     * @ORM\Column(type="string", nullable=false, length=31)
     */
    private $domicile;
    /**
     * @ORM\Column(type="string", nullable=false, length=31)
     */
    private $club;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $result;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $splits;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $finish_split;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $class_distance;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $class_elevation;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $class_control_count;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $class_competitor_count;
    // PRIMARY KEY (`id`),
    // UNIQUE KEY `person` (`person`,`event`,`class`,`name`,`birth_year`,`domicile`,`club`)
}
