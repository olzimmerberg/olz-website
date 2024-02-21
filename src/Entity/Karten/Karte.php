<?php

namespace Olz\Entity\Karten;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Repository\Karten\KartenRepository;

#[ORM\Table(name: 'karten')]
#[ORM\Entity(repositoryClass: KartenRepository::class)]
class Karte extends OlzEntity {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $position;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $kartennr;

    #[ORM\Column(type: 'string', nullable: false)]
    private $name;

    // @deprecated
    #[ORM\Column(type: 'integer', nullable: true)]
    private $center_x;

    // @deprecated
    #[ORM\Column(type: 'integer', nullable: true)]
    private $center_y;

    #[ORM\Column(type: 'float', nullable: true)]
    private $latitude;

    #[ORM\Column(type: 'float', nullable: true)]
    private $longitude;

    #[ORM\Column(type: 'string', nullable: true)]
    private $jahr;

    #[ORM\Column(type: 'string', nullable: true)]
    private $massstab;

    #[ORM\Column(type: 'string', nullable: true)]
    private $ort;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $zoom;

    #[ORM\Column(type: 'string', nullable: true)]
    private $typ;

    #[ORM\Column(type: 'string', nullable: true)]
    private $vorschau;

    public function getId() {
        return $this->id;
    }

    public function setId($new_value) {
        $this->id = $new_value;
    }

    public function getPosition() {
        return $this->position;
    }

    public function setPosition($new_value) {
        $this->position = $new_value;
    }

    public function getKartenNr() {
        return $this->kartennr;
    }

    public function setKartenNr($new_value) {
        $this->kartennr = $new_value;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($new_value) {
        $this->name = $new_value;
    }

    public function getCenterX() {
        return $this->center_x;
    }

    public function setCenterX($new_value) {
        $this->center_x = $new_value;
    }

    public function getCenterY() {
        return $this->center_y;
    }

    public function setCenterY($new_value) {
        $this->center_y = $new_value;
    }

    public function getYear() {
        return $this->jahr;
    }

    public function setYear($new_value) {
        $this->jahr = $new_value;
    }

    public function getScale() {
        return $this->massstab;
    }

    public function setScale($new_value) {
        $this->massstab = $new_value;
    }

    public function getPlace() {
        return $this->ort;
    }

    public function setPlace($new_value) {
        $this->ort = $new_value;
    }

    public function getZoom() {
        return $this->zoom;
    }

    public function setZoom($new_value) {
        $this->zoom = $new_value;
    }

    public function getKind() {
        return $this->typ;
    }

    public function setKind($new_value) {
        $this->typ = $new_value;
    }

    public function getPreviewImageId() {
        return $this->vorschau;
    }

    public function setPreviewImageId($new_value) {
        $this->vorschau = $new_value;
    }
}
