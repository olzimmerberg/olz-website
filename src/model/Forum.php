<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="forum",
 *     indexes={@ORM\Index(name="datum_on_off_index", columns={"datum", "on_off"})},
 * )
 */
class Forum {
    /**
     * @ORM\Column(type="string", nullable=false, options={"default": ""})
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
     * @ORM\Column(type="boolean", nullable=false, options={"default": 1})
     */
    private $newsletter;
    /**
     * @ORM\Column(type="string", nullable=false, options={"default": ""})
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
     * @ORM\Column(type="string", nullable=false, options={"default": ""})
     */
    private $name2;
    // PRIMARY KEY (`id`),
    // KEY `datum` (`datum`),
    // KEY `on_off` (`on_off`)

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getDate() {
        return $this->datum;
    }

    public function setDate($new_datum) {
        $this->datum = $new_datum;
    }

    public function getTime() {
        return $this->zeit;
    }

    public function setTime($new_zeit) {
        $this->zeit = $new_zeit;
    }

    public function getTitle() {
        return $this->name;
    }

    public function setTitle($new_name) {
        $this->name = $new_name;
    }

    public function getAuthor() {
        return $this->name2;
    }

    public function setAuthor($new_name2) {
        $this->name2 = $new_name2;
    }

    public function getContent() {
        return $this->eintrag;
    }

    public function setContent($new_eintrag) {
        $this->eintrag = $new_eintrag;
    }

    public function getOnOff() {
        return $this->on_off;
    }

    public function setOnOff($new_on_off) {
        $this->on_off = $new_on_off;
    }
}
