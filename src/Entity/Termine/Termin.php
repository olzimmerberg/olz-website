<?php

namespace Olz\Entity\Termine;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Anmelden\Registration;
use Olz\Entity\OlzEntity;
use Olz\Repository\Termine\TerminRepository;

#[ORM\Table(name: 'termine')]
#[ORM\Index(name: 'datum_on_off_index', columns: ['datum', 'on_off'])]
#[ORM\Entity(repositoryClass: TerminRepository::class)]
class Termin extends OlzEntity {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'date', nullable: true)]
    private $datum;

    #[ORM\Column(type: 'date', nullable: true)]
    private $datum_end;

    #[ORM\Column(type: 'date', nullable: true)]
    private $datum_off;

    #[ORM\Column(type: 'time', nullable: true, options: ['default' => '00:00:00'])]
    private $zeit;

    #[ORM\Column(type: 'time', nullable: true, options: ['default' => '00:00:00'])]
    private $zeit_end;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $deadline;

    #[ORM\ManyToOne(targetEntity: Registration::class)]
    #[ORM\JoinColumn(name: 'participants_registration_id', referencedColumnName: 'id')]
    private $participants_registration;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $num_participants;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $min_participants;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $max_participants;

    #[ORM\ManyToOne(targetEntity: Registration::class)]
    #[ORM\JoinColumn(name: 'volunteers_registration_id', referencedColumnName: 'id')]
    private $volunteers_registration;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $num_volunteers;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $min_volunteers;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $max_volunteers;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    private $newsletter;

    #[ORM\Column(type: 'text', nullable: true)]
    private $titel;

    #[ORM\Column(type: 'text', nullable: true)]
    private $go2ol;

    #[ORM\Column(type: 'text', nullable: true)]
    private $text;

    #[ORM\Column(type: 'text', nullable: true)]
    private $link;

    #[ORM\Column(type: 'text', nullable: true)]
    private $solv_event_link;

    #[ORM\Column(type: 'string', nullable: true)]
    private $typ;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $xkoord;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $ykoord;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $solv_uid;

    #[ORM\Column(type: 'string', nullable: true)]
    private $ical_uid;

    // PRIMARY KEY (`id`),
    // KEY `datum` (`datum`),
    // KEY `on_off` (`on_off`),
    // KEY `datum_end` (`datum_end`),
    // KEY `datum_off` (`datum_off`)

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getStartsOn() {
        return $this->datum;
    }

    public function setStartsOn($new_datum) {
        $this->datum = $new_datum;
    }

    // @deprecated Move to datetime
    public function getStartTime() {
        return $this->zeit;
    }

    // @deprecated Move to datetime
    public function setStartTime($new_zeit) {
        $this->zeit = $new_zeit;
    }

    public function getEndsOn() {
        return $this->datum_end;
    }

    public function setEndsOn($new_datum_end) {
        $this->datum_end = $new_datum_end;
    }

    // @deprecated Move to datetime
    public function getEndTime() {
        return $this->zeit_end;
    }

    // @deprecated Move to datetime
    public function setEndTime($new_zeit_end) {
        $this->zeit_end = $new_zeit_end;
    }

    public function getDeadline() {
        return $this->deadline;
    }

    public function setDeadline($new_deadline) {
        $this->deadline = $new_deadline;
    }

    public function getTitle() {
        return $this->titel;
    }

    public function setTitle($new_titel) {
        $this->titel = $new_titel;
    }

    public function getText() {
        return $this->text;
    }

    public function setText($new_text) {
        $this->text = $new_text;
    }

    public function getLink() {
        return $this->link;
    }

    public function setLink($new_link) {
        $this->link = $new_link;
    }

    public function getTypes() {
        return $this->typ;
    }

    public function setTypes($new_types) {
        $this->typ = $new_types;
    }

    public function getSolvId() {
        return $this->solv_uid;
    }

    public function setSolvId($new_solv_uid) {
        $this->solv_uid = $new_solv_uid;
    }

    // @deprecated Use SolvId to get the go2ol (or other platform) ID
    public function getGo2olId() {
        return $this->go2ol;
    }

    // @deprecated Use SolvId to get the go2ol (or other platform) ID
    public function setGo2olId($new_go2ol) {
        $this->go2ol = $new_go2ol;
    }

    public function getCoordinateX() {
        return $this->xkoord;
    }

    public function setCoordinateX($new_xkoord) {
        $this->xkoord = $new_xkoord;
    }

    public function getCoordinateY() {
        return $this->ykoord;
    }

    public function setCoordinateY($new_ykoord) {
        $this->ykoord = $new_ykoord;
    }

    public function getOnOff() {
        return $this->on_off;
    }

    public function setOnOff($new_on_off) {
        $this->on_off = $new_on_off;
    }

    public function getNewsletter() {
        return $this->newsletter;
    }

    public function setNewsletter($new_newsletter) {
        $this->newsletter = $new_newsletter;
    }
}
