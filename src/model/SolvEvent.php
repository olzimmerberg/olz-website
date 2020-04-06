<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="solv_events")
 */
class SolvEvent {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false)
     */
    private $solv_uid;
    /**
     * @ORM\Column(type="date", nullable=false)
     */
    private $date;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $duration;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $kind;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $day_night;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $national;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $region;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $type;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $name;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $link;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $club;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $map;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $location;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $coord_x;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $coord_y;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $deadline;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $entryportal;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $start_link;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $rank_link;
    /**
     * @ORM\Column(type="datetime", nullable=false, options={"default":"CURRENT_TIMESTAMP"})
     */
    private $last_modification;
    // PRIMARY KEY (`solv_uid`)
}
