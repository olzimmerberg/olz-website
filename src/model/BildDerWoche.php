<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="bild_der_woche",
 *   indexes={@ORM\Index(name="datum_index", columns={"datum"})},
 * )
 */
class bild_der_woche {
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datum;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bild1;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bild2;
    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $on_off;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $titel;
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bild1_breite;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bild2_breite;
    // PRIMARY KEY (`id`),
    // KEY `datum` (`datum`)
}
