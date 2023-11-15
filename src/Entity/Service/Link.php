<?php

namespace Olz\Entity\Service;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;

#[ORM\Table(name: 'links')]
#[ORM\Entity]
class Link extends OlzEntity {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'text', nullable: true)]
    private $name;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $position;

    #[ORM\Column(type: 'text', nullable: true)]
    private $url;

    public function getId() {
        return $this->id;
    }

    public function setId($new_value) {
        $this->id = $new_value;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($new_value) {
        $this->name = $new_value;
    }

    public function getPosition() {
        return $this->position;
    }

    public function setPosition($new_value) {
        $this->position = $new_value;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($new_value) {
        $this->url = $new_value;
    }
}
