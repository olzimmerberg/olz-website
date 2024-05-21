<?php

namespace Olz\Entity\Faq;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;

#[ORM\Table(name: 'question_categories')]
#[ORM\Index(name: 'position_index', columns: ['on_off', 'position'])]
#[ORM\Entity]
class QuestionCategory extends OlzEntity {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $position;

    #[ORM\Column(type: 'string', nullable: false)]
    private $name;

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

    public function getName() {
        return $this->name;
    }

    public function setName($new_value) {
        $this->name = $new_value;
    }
}
