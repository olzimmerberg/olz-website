<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="olz_text",
 * )
 */
class OlzText {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false)
     */
    private $id;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;
    /**
     * @ORM\Column(type="integer", options={"default": 1})
     */
    private $on_off;
    // PRIMARY KEY (`id`)

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getText() {
        return $this->text;
    }

    public function setText($new_text) {
        $this->text = $new_text;
    }

    public function getOnOff() {
        return $this->on_off;
    }

    public function setOnOff($new_on_off) {
        $this->on_off = $new_on_off;
    }
}
