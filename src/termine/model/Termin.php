<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="termine",
 *     indexes={@ORM\Index(name="datum_on_off_index", columns={"datum", "on_off"})},
 * )
 */
class Termin {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datum;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datum_end;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datum_off;
    /**
     * @ORM\Column(type="time", nullable=true, options={"default": "00:00:00"})
     */
    private $zeit;
    /**
     * @ORM\Column(type="time", nullable=true, options={"default": "00:00:00"})
     */
    private $zeit_end;
    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $teilnehmer;
    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": 0})
     */
    private $newsletter;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $titel;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $go2ol;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $link;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $solv_event_link;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $typ;
    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $on_off;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datum_anmeldung;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text_anmeldung;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $email_anmeldung;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $xkoord;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ykoord;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $solv_uid;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $ical_uid;
    /**
     * @ORM\Column(type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $modified; // ON UPDATE current_timestamp(),
    /**
     * @ORM\Column(type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $created;
    // PRIMARY KEY (`id`),
    // KEY `datum` (`datum`),
    // KEY `on_off` (`on_off`),
    // KEY `datum_end` (`datum_end`),
    // KEY `datum_off` (`datum_off`)

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getStartsOn() {
        return $this->datum;
    }

    public function setStartsOn($new_datum) {
        $this->datum = $new_datum;
    }

    public function getEndsOn() {
        return $this->datum_end;
    }

    public function setEndsOn($new_datum_end) {
        $this->datum_end = $new_datum_end;
    }

    public function getTitle() {
        return $this->titel;
    }

    public function setTitle($new_titel) {
        $this->titel = $new_titel;
    }
}
