<?php

namespace Olz\Entity\Anmelden;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Repository\Anmelden\RegistrationRepository;

#[ORM\Table(name: 'anmelden_registrations')]
#[ORM\Index(name: 'opens_at_index', columns: ['opens_at'])]
#[ORM\Index(name: 'closes_at_index', columns: ['closes_at'])]
#[ORM\Entity(repositoryClass: RegistrationRepository::class)]
class Registration extends OlzEntity {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'text', nullable: false)]
    private $title;

    #[ORM\Column(type: 'text', nullable: false)]
    private $description;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $opens_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $closes_at;

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($new_title) {
        $this->title = $new_title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($new_description) {
        $this->description = $new_description;
    }

    public function getOpensAt() {
        return $this->opens_at;
    }

    public function setOpensAt($new_opens_at) {
        $this->opens_at = $new_opens_at;
    }

    public function getClosesAt() {
        return $this->closes_at;
    }

    public function setClosesAt($new_closes_at) {
        $this->closes_at = $new_closes_at;
    }
}
