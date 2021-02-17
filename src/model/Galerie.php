<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="GalerieRepository")
 * @ORM\Table(
 *     name="galerie",
 *     indexes={@ORM\Index(name="datum_on_off_index", columns={"datum", "on_off"})},
 * )
 */
class Galerie {
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $termin;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $titel;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datum;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datum_end;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $autor;
    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $on_off;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $typ;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $counter;
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;
    // PRIMARY KEY (`id`),
    // KEY `datum` (`datum`),
    // KEY `on_off` (`on_off`)
}
