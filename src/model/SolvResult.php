<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="solv_results")
 */
class solv_results {
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
     * @ORM\Column(type="string", nullable=false)
     */
    private $class;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $rank;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $birth_year;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $domicile;
    /**
     * @ORM\Column(type="string", nullable=false)
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
