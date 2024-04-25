<?php

namespace Olz\Entity\Panini2024;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Repository\Panini2024\Panini2024PictureRepository;

#[ORM\Table(name: 'panini24')]
#[ORM\Entity(repositoryClass: Panini2024PictureRepository::class)]
class Panini2024Picture extends OlzEntity {
    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'string', nullable: false)]
    private $line1;

    #[ORM\Column(type: 'string', nullable: true)]
    private $line2;

    #[ORM\Column(type: 'string', nullable: true)]
    private $association;

    #[ORM\Column(type: 'string', nullable: false)]
    private $img_src;

    #[ORM\Column(type: 'string', nullable: false)]
    private $img_style;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private $is_landscape;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private $has_top;

    #[ORM\Column(type: 'date', nullable: true)]
    private $birthdate;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $num_mispunches;

    #[ORM\Column(type: 'text', nullable: false)]
    private $infos;

    public function getId() {
        return $this->id;
    }

    public function setId($new_value) {
        $this->id = $new_value;
    }

    public function getLine1() {
        return $this->line1;
    }

    public function setLine1($new_value) {
        $this->line1 = $new_value;
    }

    public function getLine2() {
        return $this->line2;
    }

    public function setLine2($new_value) {
        $this->line2 = $new_value;
    }

    public function getAssociation() {
        return $this->association;
    }

    public function setAssociation($new_value) {
        $this->association = $new_value;
    }

    public function getImgSrc() {
        return $this->img_src;
    }

    public function setImgSrc($new_value) {
        $this->img_src = $new_value;
    }

    public function getImgStyle() {
        return $this->img_style;
    }

    public function setImgStyle($new_value) {
        $this->img_style = $new_value;
    }

    public function getIsLandscape() {
        return $this->is_landscape;
    }

    public function setIsLandscape($new_value) {
        $this->is_landscape = $new_value;
    }

    public function getHasTop() {
        return $this->has_top;
    }

    public function setHasTop($new_value) {
        $this->has_top = $new_value;
    }

    public function getBirthdate() {
        return $this->birthdate;
    }

    public function setBirthdate($new_value) {
        $this->birthdate = $new_value;
    }

    public function getNumMispunches() {
        return $this->num_mispunches;
    }

    public function setNumMispunches($new_value) {
        $this->num_mispunches = $new_value;
    }

    public function getInfos() {
        return json_decode($this->infos ?? '[]', true);
    }

    public function setInfos($new_value) {
        $this->infos = json_encode($new_value);
    }
}
