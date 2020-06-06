<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="counter",
 * )
 */
class counter {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $page;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $name;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $counter;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $counter_ip;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $start_date;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $end_date;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $counter_bak;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $counter_ip_bak;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $bak_date;
    // PRIMARY KEY (`id`)
}
