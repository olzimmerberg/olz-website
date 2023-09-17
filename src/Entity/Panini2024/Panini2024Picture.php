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

    #[ORM\Column(type: 'text', nullable: false)]
    private $infos;

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getLine1() {
        return $this->line1;
    }

    public function setLine1($new_line1) {
        $this->line1 = $new_line1;
    }

    public function getLine2() {
        return $this->line2;
    }

    public function setLine2($new_line2) {
        $this->line2 = $new_line2;
    }

    public function getAssociation() {
        return $this->association;
    }

    public function setAssociation($new_association) {
        $this->association = $new_association;
    }

    public function getImgSrc() {
        return $this->img_src;
    }

    public function setImgSrc($new_img_src) {
        $this->img_src = $new_img_src;
    }

    public function getImgStyle() {
        return $this->img_style;
    }

    public function setImgStyle($new_img_style) {
        $this->img_style = $new_img_style;
    }

    public function getIsLandscape() {
        return $this->is_landscape;
    }

    public function setIsLandscape($new_is_landscape) {
        $this->is_landscape = $new_is_landscape;
    }

    public function getHasTop() {
        return $this->has_top;
    }

    public function setHasTop($new_has_top) {
        $this->has_top = $new_has_top;
    }

    public function getInfos() {
        return json_decode($this->infos ?? '[]', true);
    }

    public function setInfos($new_infos) {
        $this->infos = json_encode($new_infos);
    }
}
