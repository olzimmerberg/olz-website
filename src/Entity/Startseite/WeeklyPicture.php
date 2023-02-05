<?php

namespace Olz\Entity\Startseite;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\OlzEntity;
use Olz\Repository\Startseite\WeeklyPictureRepository;

/**
 * @ORM\Entity(repositoryClass=WeeklyPictureRepository::class)
 *
 * @ORM\Table(
 *     name="weekly_picture",
 *     indexes={@ORM\Index(name="datum_index", columns={"datum"})},
 * )
 */
class WeeklyPicture extends OlzEntity {
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datum;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $image_id;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $alternative_image_id;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
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

    public function getText() {
        return $this->text;
    }

    public function setText($new_text) {
        $this->text = $new_text;
    }

    public function getImageId() {
        return $this->image_id;
    }

    public function setImageId($new_image_id) {
        $this->image_id = $new_image_id;
    }

    public function getAlternativeImageId() {
        return $this->alternative_image_id;
    }

    public function setAlternativeImageId($new_alternative_image_id) {
        $this->alternative_image_id = $new_alternative_image_id;
    }
}
