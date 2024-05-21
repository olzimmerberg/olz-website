<?php

namespace Olz\Entity\Faq;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;

#[ORM\Table(name: 'questions')]
#[ORM\Index(name: 'ident_index', columns: ['on_off', 'ident'])]
#[ORM\Index(name: 'category_position_index', columns: ['on_off', 'category_id', 'position_within_category'])]
#[ORM\Entity]
class Question extends OlzEntity {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'string', length: 31, nullable: false)]
    private $ident;

    #[ORM\ManyToOne(targetEntity: QuestionCategory::class)]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: true)]
    private $category;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $position_within_category;

    #[ORM\Column(type: 'text', nullable: false)]
    private $question;

    #[ORM\Column(type: 'text', nullable: true)]
    private $answer;

    public function getId() {
        return $this->id;
    }

    public function setId($new_value) {
        $this->id = $new_value;
    }

    public function getIdent() {
        return $this->ident;
    }

    public function setIdent($new_value) {
        $this->ident = $new_value;
    }

    public function getQuestion() {
        return $this->question;
    }

    public function setQuestion($new_value) {
        $this->question = $new_value;
    }

    public function getCategory() {
        return $this->category;
    }

    public function setCategory($new_value) {
        $this->category = $new_value;
    }

    public function getPositionWithinCategory() {
        return $this->position_within_category;
    }

    public function setPositionWithinCategory($new_value) {
        $this->position_within_category = $new_value;
    }

    public function getAnswer() {
        return $this->answer;
    }

    public function setAnswer($new_value) {
        $this->answer = $new_value;
    }
}
