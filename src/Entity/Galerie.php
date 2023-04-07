<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Repository\GalerieRepository;

#[ORM\Table(name: 'galerie')]
#[ORM\Index(name: 'datum_on_off_index', columns: ['datum', 'on_off'])]
#[ORM\Entity(repositoryClass: GalerieRepository::class)]
class Galerie {
    #[ORM\Column(type: 'integer', nullable: false)]
    private $termin;

    #[ORM\Column(type: 'text', nullable: false)]
    private $titel;

    #[ORM\Column(type: 'date', nullable: true)]
    private $datum;

    #[ORM\Column(type: 'date', nullable: true)]
    private $datum_end;

    #[ORM\Column(type: 'text', nullable: true)]
    private $autor;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private $on_off;

    #[ORM\Column(type: 'text', nullable: true)]
    private $typ;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $counter;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'text', nullable: true)]
    private $content;
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

    public function getOnOff() {
        return $this->on_off;
    }

    public function setOnOff($new_on_off) {
        $this->on_off = $new_on_off;
    }
}
