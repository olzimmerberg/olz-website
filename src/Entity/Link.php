<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 *
 * @ORM\Table(
 *     name="links",
 * )
 */
class Link {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $name;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $url;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datum;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $on_off;
    // PRIMARY KEY (`id`)
}
