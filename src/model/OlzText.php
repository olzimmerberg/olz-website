<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="olz_text")
 */
class olz_text {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;
    /**
     * @ORM\Column(type="integer", options={"default":1})
     */
    private $on_off;
    // PRIMARY KEY (`id`)
}
