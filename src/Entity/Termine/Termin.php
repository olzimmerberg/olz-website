<?php

namespace Olz\Entity\Termine;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Anmelden\Registration;
use Olz\Entity\Common\OlzEntity;
use Olz\Repository\Termine\TerminRepository;

#[ORM\Table(name: 'termine')]
#[ORM\Index(name: 'datum_on_off_index', columns: ['datum', 'on_off'])]
#[ORM\Entity(repositoryClass: TerminRepository::class)]
class Termin extends OlzEntity {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    // @deprecated Use start_date
    #[ORM\Column(type: 'date', nullable: true)]
    private $datum;

    // @deprecated Use end_date
    #[ORM\Column(type: 'date', nullable: true)]
    private $datum_end;

    // @deprecated No replacement
    #[ORM\Column(type: 'date', nullable: true)]
    private $datum_off;

    // @deprecated Use start_time
    #[ORM\Column(type: 'time', nullable: true, options: ['default' => '00:00:00'])]
    private $zeit;

    // @deprecated Use end_time
    #[ORM\Column(type: 'time', nullable: true, options: ['default' => '00:00:00'])]
    private $zeit_end;

    #[ORM\Column(type: 'date', nullable: false)]
    private $start_date;

    #[ORM\Column(type: 'time', nullable: true)]
    private $start_time;

    #[ORM\Column(type: 'date', nullable: true)]
    private $end_date;

    #[ORM\Column(type: 'time', nullable: true)]
    private $end_time;

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

    // @deprecated Use title
    #[ORM\Column(type: 'text', nullable: true)]
    private $titel;

    #[ORM\Column(type: 'text', nullable: true)]
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private $go2ol;

    #[ORM\Column(type: 'text', nullable: true)]
    private $text;

    #[ORM\Column(type: 'text', nullable: true)]
    private $link;

    // @deprecated Use solv_uid
    #[ORM\Column(type: 'text', nullable: true)]
    private $solv_event_link;

    // @deprecated Use labels
    #[ORM\Column(type: 'string', nullable: true)]
    private $typ;

    #[ORM\JoinTable(name: 'termin_label_map')]
    #[ORM\JoinColumn(name: 'termin_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'label_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: TerminLabel::class, inversedBy: 'termine')]
    private $labels;

    #[ORM\ManyToOne(targetEntity: TerminLocation::class)]
    #[ORM\JoinColumn(name: 'location_id', referencedColumnName: 'id', nullable: true)]
    private $location;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $xkoord;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $ykoord;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $solv_uid;

    // @deprecated No replacement
    #[ORM\Column(type: 'string', nullable: true)]
    private $ical_uid;

    #[ORM\Column(type: 'text', nullable: true)]
    private $image_ids;

    // PRIMARY KEY (`id`),
    // KEY `datum` (`datum`),
    // KEY `on_off` (`on_off`),
    // KEY `datum_end` (`datum_end`),
    // KEY `datum_off` (`datum_off`)

    public function getId() {
        return $this->id;
    }

    public function setId($new_value) {
        $this->id = $new_value;
    }

    public function getStartDate() {
        return $this->start_date;
    }

    public function setStartDate($new_value) {
        $this->datum = $new_value;
        $this->start_date = $new_value;
    }

    public function getStartTime() {
        return $this->start_time;
    }

    public function setStartTime($new_value) {
        $this->zeit = $new_value;
        $this->start_time = $new_value;
    }

    public function getEndDate() {
        return $this->end_date;
    }

    public function setEndDate($new_value) {
        $this->datum_end = $new_value;
        $this->end_date = $new_value;
    }

    public function getEndTime() {
        return $this->end_time;
    }

    public function setEndTime($new_value) {
        $this->zeit_end = $new_value;
        $this->end_time = $new_value;
    }

    public function getDeadline() {
        return $this->deadline;
    }

    public function setDeadline($new_value) {
        $this->deadline = $new_value;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($new_value) {
        $this->titel = $new_value;
        $this->title = $new_value;
    }

    public function getText() {
        return $this->text;
    }

    public function setText($new_value) {
        $this->text = $new_value;
    }

    public function getLink() {
        return $this->link;
    }

    public function setLink($new_value) {
        $this->link = $new_value;
    }

    public function getTypes() {
        return $this->typ;
    }

    public function setTypes($new_value) {
        $this->typ = $new_value;
    }

    public function getSolvId() {
        return $this->solv_uid;
    }

    public function setSolvId($new_value) {
        $this->solv_uid = $new_value;
    }

    // @deprecated Use SolvId to get the go2ol (or other platform) ID
    public function getGo2olId() {
        return $this->go2ol;
    }

    // @deprecated Use SolvId to get the go2ol (or other platform) ID
    public function setGo2olId($new_value) {
        $this->go2ol = $new_value;
    }

    public function getCoordinateX() {
        return $this->xkoord;
    }

    public function setCoordinateX($new_value) {
        $this->xkoord = $new_value;
    }

    public function getCoordinateY() {
        return $this->ykoord;
    }

    public function setCoordinateY($new_value) {
        $this->ykoord = $new_value;
    }

    public function getLocation() {
        return $this->location;
    }

    public function setLocation(?TerminLocation $new_value) {
        $this->location = $new_value;
    }

    public function getNewsletter() {
        return $this->newsletter;
    }

    public function setNewsletter($new_value) {
        $this->newsletter = $new_value;
    }

    public function getImageIds() {
        if ($this->image_ids == null) {
            return null;
        }
        return json_decode($this->image_ids, true);
    }

    public function setImageIds($new_value) {
        $this->image_ids = json_encode($new_value);
    }
}
