<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="blog",
 * )
 */
class blog {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $counter;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datum;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $autor;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $titel;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bild1;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bild2;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $on_off;
    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $zeit;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dummy;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $file1;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $file1_name;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $file2;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $file2_name;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $newsletter;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $newsletter_datum;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bild1_breite;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bild2_breite;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $linkext;
    // PRIMARY KEY (`id`)
}
