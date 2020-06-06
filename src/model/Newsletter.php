<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="newsletter",
 * )
 */
class newsletter {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $email;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $kategorie;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $reg_date;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $uid;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $on_off;
    // PRIMARY KEY (`id`)
}
