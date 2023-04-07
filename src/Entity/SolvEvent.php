<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Repository\SolvEventRepository;

require_once __DIR__.'/common.php';

#[ORM\Table(name: 'solv_events')]
#[ORM\Entity(repositoryClass: SolvEventRepository::class)]
class SolvEvent {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    private $solv_uid;

    #[ORM\Column(type: 'date', nullable: false)]
    private $date;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $duration;

    #[ORM\Column(type: 'text', nullable: false)]
    private $kind;

    #[ORM\Column(type: 'text', nullable: false)]
    private $day_night;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $national;

    #[ORM\Column(type: 'text', nullable: false)]
    private $region;

    #[ORM\Column(type: 'text', nullable: false)]
    private $type;

    #[ORM\Column(type: 'text', nullable: false)]
    private $name;

    #[ORM\Column(type: 'text', nullable: false)]
    private $link;

    #[ORM\Column(type: 'text', nullable: false)]
    private $club;

    #[ORM\Column(type: 'text', nullable: false)]
    private $map;

    #[ORM\Column(type: 'text', nullable: false)]
    private $location;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $coord_x;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $coord_y;

    #[ORM\Column(type: 'date', nullable: true)]
    private $deadline;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $entryportal;

    #[ORM\Column(type: 'text', nullable: true)]
    private $start_link;

    #[ORM\Column(type: 'text', nullable: true)]
    private $rank_link;

    #[ORM\Column(type: 'datetime', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private $last_modification;

    private $valid_field_names = [
        'solv_uid' => true,
        'date' => true,
        'duration' => true,
        'kind' => true,
        'day_night' => true,
        'national' => true,
        'region' => true,
        'type' => true,
        'name' => true,
        'link' => true,
        'club' => true,
        'map' => true,
        'location' => true,
        'coord_x' => true,
        'coord_y' => true,
        'deadline' => true,
        'entryportal' => true,
        'start_link' => true,
        'rank_link' => true,
        'last_modification' => true,
    ];
    // PRIMARY KEY (`solv_uid`)

    public function getSolvUid() {
        return $this->solv_uid;
    }

    public function setSolvUid($new_solv_uid) {
        $this->solv_uid = $new_solv_uid;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($new_date) {
        $this->date = sanitize_date_value($new_date);
    }

    public function getDuration() {
        return $this->duration;
    }

    public function setDuration($new_duration) {
        $this->duration = $new_duration;
    }

    public function getKind() {
        return $this->kind;
    }

    public function setKind($new_kind) {
        $this->kind = $new_kind;
    }

    public function getDayNight() {
        return $this->day_night;
    }

    public function setDayNight($new_day_night) {
        $this->day_night = $new_day_night;
    }

    public function getNational() {
        return $this->national;
    }

    public function setNational($new_national) {
        $this->national = $new_national;
    }

    public function getRegion() {
        return $this->region;
    }

    public function setRegion($new_region) {
        $this->region = $new_region;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($new_type) {
        $this->type = $new_type;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($new_name) {
        $this->name = $new_name;
    }

    public function getLink() {
        return $this->link;
    }

    public function setLink($new_link) {
        $this->link = $new_link;
    }

    public function getClub() {
        return $this->club;
    }

    public function setClub($new_club) {
        $this->club = $new_club;
    }

    public function getMap() {
        return $this->map;
    }

    public function setMap($new_map) {
        $this->map = $new_map;
    }

    public function getLocation() {
        return $this->location;
    }

    public function setLocation($new_location) {
        $this->location = $new_location;
    }

    public function getCoordX() {
        return $this->coord_x;
    }

    public function setCoordX($new_coord_x) {
        $this->coord_x = $new_coord_x;
    }

    public function getCoordY() {
        return $this->coord_y;
    }

    public function setCoordY($new_coord_y) {
        $this->coord_y = $new_coord_y;
    }

    public function getDeadline() {
        return $this->deadline;
    }

    public function setDeadline($new_deadline) {
        $this->deadline = sanitize_date_value($new_deadline);
    }

    public function getEntryportal() {
        return $this->entryportal;
    }

    public function setEntryportal($new_entryportal) {
        $this->entryportal = $new_entryportal;
    }

    public function getStartLink() {
        return $this->start_link;
    }

    public function setStartLink($new_start_link) {
        $this->start_link = $new_start_link;
    }

    public function getRankLink() {
        return $this->rank_link;
    }

    public function setRankLink($new_rank_link) {
        $this->rank_link = $new_rank_link;
    }

    public function getLastModification() {
        return $this->last_modification;
    }

    public function setLastModification($new_last_modification) {
        $this->last_modification = sanitize_datetime_value($new_last_modification);
    }

    public function getFieldValue($field_name) {
        if (!isset($this->valid_field_names[$field_name])) {
            throw new \Exception("getFieldValue: Invalid field name: {$field_name}", 1);
        }
        return $this->{$field_name};
    }

    public function setFieldValue($field_name, $new_field_value) {
        if (!isset($this->valid_field_names[$field_name])) {
            throw new \Exception("setFieldValue: Invalid field name: {$field_name}", 1);
        }
        $this->{$field_name} = $new_field_value;
    }
}
