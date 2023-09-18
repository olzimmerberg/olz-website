<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'blog')]
#[ORM\Entity]
class Blog {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private $counter;

    #[ORM\Column(type: 'date', nullable: true)]
    private $datum;

    #[ORM\Column(type: 'text', nullable: true)]
    private $autor;

    #[ORM\Column(type: 'text', nullable: true)]
    private $titel;

    #[ORM\Column(type: 'text', nullable: true)]
    private $text;

    #[ORM\Column(type: 'text', nullable: true)]
    private $bild1;

    #[ORM\Column(type: 'text', nullable: true)]
    private $bild2;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $on_off;

    #[ORM\Column(type: 'time', nullable: true)]
    private $zeit;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $dummy;

    #[ORM\Column(type: 'text', nullable: true)]
    private $file1;

    #[ORM\Column(type: 'text', nullable: true)]
    private $file1_name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $file2;

    #[ORM\Column(type: 'text', nullable: true)]
    private $file2_name;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 1])]
    private $newsletter;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $bild1_breite;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $bild2_breite;

    #[ORM\Column(type: 'text', nullable: true)]
    private $linkext;
    // PRIMARY KEY (`id`)

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getPublishedDate() {
        return $this->datum;
    }

    public function setPublishedDate($new_datum) {
        $this->datum = $new_datum;
    }

    public function getPublishedTime() {
        return $this->zeit;
    }

    public function setPublishedTime($new_zeit) {
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

    public function getContent() {
        return $this->text;
    }

    public function setContent($new_text) {
        $this->text = $new_text;
    }

    public function getOnOff() {
        return $this->on_off;
    }

    public function setOnOff($new_on_off) {
        $this->on_off = $new_on_off;
    }
}
