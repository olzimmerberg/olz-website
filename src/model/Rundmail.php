<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="rundmail")
 */
class rundmail {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $betreff;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $mailtext;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datum;
    // PRIMARY KEY (`id`)
}
