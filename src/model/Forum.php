<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="forum",
 *   indexes={@ORM\Index(name="datum_on_off_index", columns={"datum", "on_off"})},
 * )
 */
class forum {
    /**
     * @ORM\Column(type="string", nullable=false, options={"default":""})
     */
    private $name;
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $email;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $eintrag;
    /**
     * @ORM\Column(type="integer", options={"default":1})
     */
    private $newsletter;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $newsletter_datum;
    /**
     * @ORM\Column(type="string", nullable=false, options={"default":""})
     */
    private $uid;
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datum;
    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $zeit;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $on_off;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $allowHTML;
    /**
     * @ORM\Column(type="string", nullable=false, options={"default":""})
     */
    private $name2;
    // PRIMARY KEY (`id`),
    // KEY `datum` (`datum`),
    // KEY `on_off` (`on_off`)
}
