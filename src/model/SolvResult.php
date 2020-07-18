<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="solv_results",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="person_run_unique", columns={"person", "event", "class", "name", "birth_year", "domicile", "club"})},
 * )
 */
class SolvResult {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $person;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $event;
    /**
     * @ORM\Column(type="string", nullable=false, length=15)
     */
    private $class;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $rank;
    /**
     * @ORM\Column(type="string", nullable=false, length=31)
     */
    private $name;
    /**
     * @ORM\Column(type="string", nullable=false, length=3)
     */
    private $birth_year;
    /**
     * @ORM\Column(type="string", nullable=false, length=31)
     */
    private $domicile;
    /**
     * @ORM\Column(type="string", nullable=false, length=31)
     */
    private $club;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $result;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $splits;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $finish_split;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $class_distance;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $class_elevation;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $class_control_count;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $class_competitor_count;
    // PRIMARY KEY (`id`),
    // UNIQUE KEY `person` (`person`,`event`,`class`,`name`,`birth_year`,`domicile`,`club`)

    public function getPerson() {
        return $this->person;
    }

    public function setPerson($new_person) {
        $this->person = $new_person;
    }

    public function getEvent() {
        return $this->event;
    }

    public function setEvent($new_event) {
        $this->event = $new_event;
    }

    public function getClass() {
        return $this->class;
    }

    public function setClass($new_class) {
        $this->class = $new_class;
    }

    public function getRank() {
        return $this->rank;
    }

    public function setRank($new_rank) {
        $this->rank = $new_rank;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($new_name) {
        $this->name = $new_name;
    }

    public function getBirthYear() {
        return $this->birth_year;
    }

    public function setBirthYear($new_birth_year) {
        $this->birth_year = $new_birth_year;
    }

    public function getDomicile() {
        return $this->domicile;
    }

    public function setDomicile($new_domicile) {
        $this->domicile = $new_domicile;
    }

    public function getClub() {
        return $this->club;
    }

    public function setClub($new_club) {
        $this->club = $new_club;
    }

    public function getResult() {
        return $this->result;
    }

    public function setResult($new_result) {
        $this->result = $new_result;
    }

    public function getSplits() {
        return $this->splits;
    }

    public function setSplits($new_splits) {
        $this->splits = $new_splits;
    }

    public function getFinishSplit() {
        return $this->finish_split;
    }

    public function setFinishSplit($new_finish_split) {
        $this->finish_split = $new_finish_split;
    }

    public function getClassDistance() {
        return $this->class_distance;
    }

    public function setClassDistance($new_class_distance) {
        $this->class_distance = $new_class_distance;
    }

    public function getClassElevation() {
        return $this->class_elevation;
    }

    public function setClassElevation($new_class_elevation) {
        $this->class_elevation = $new_class_elevation;
    }

    public function getClassControlCount() {
        return $this->class_control_count;
    }

    public function setClassControlCount($new_class_control_count) {
        $this->class_control_count = $new_class_control_count;
    }

    public function getClassCompetitorCount() {
        return $this->class_competitor_count;
    }

    public function setClassCompetitorCount($new_class_competitor_count) {
        $this->class_competitor_count = $new_class_competitor_count;
    }
}
