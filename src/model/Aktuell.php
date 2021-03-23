<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AktuellRepository")
 * @ORM\Table(
 *     name="aktuell",
 *     indexes={@ORM\Index(name="datum_index", columns={"datum"})},
 * )
 */
class aktuell {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $termin;
    /**
     * @ORM\Column(type="date", nullable=false)
     */
    private $datum;
    /**
     * @ORM\Column(type="integer", options={"default": 1})
     */
    private $newsletter;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $newsletter_datum;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $titel;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $textlang;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $link;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $autor;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $typ;
    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $on_off;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bild1;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bild1_breite;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bild1_text;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bild2;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bild2_breite;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bild3;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bild3_breite;
    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $zeit;
    /**
     * @ORM\Column(type="integer", nullable=false, options={"default": 0})
     */
    private $counter;
    // PRIMARY KEY (`id`),
    // KEY `datum` (`datum`)

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
        return $this->titel;
    }

    public function setTitle($new_titel) {
        $this->titel = $new_titel;
    }

    public function getAuthor() {
        return $this->autor;
    }

    public function setAuthor($new_autor) {
        $this->autor = $new_autor;
    }

    public function getTeaser() {
        return $this->text;
    }

    public function setTeaser($new_text) {
        $this->text = $new_text;
    }

    public function getContent() {
        return $this->textlang;
    }

    public function setContent($new_textlang) {
        $this->textlang = $new_textlang;
    }

    public function getOnOff() {
        return $this->on_off;
    }

    public function setOnOff($new_on_off) {
        $this->on_off = $new_on_off;
    }
}
