<?php

namespace App\Entity;

use App\Repository\ThrottlingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ThrottlingRepository::class)
 * @ORM\Table(
 *     name="throttlings",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="event_name_index", columns={"event_name"}),
 *     },
 * )
 */
class Throttling {
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $event_name;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_occurrence;
    /**
     * @ORM\Id @ORM\Column(type="bigint", nullable=false) @ORM\GeneratedValue
     */
    private $id;

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getEventName() {
        return $this->event_name;
    }

    public function setEventName($new_event_name) {
        $this->event_name = $new_event_name;
    }

    public function getLastOccurrence() {
        return $this->last_occurrence;
    }

    public function setLastOccurrence($new_last_occurrence) {
        $this->last_occurrence = $new_last_occurrence;
    }
}
