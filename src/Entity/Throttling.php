<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\TestableInterface;
use Olz\Repository\ThrottlingRepository;

#[ORM\Table(name: 'throttlings')]
#[ORM\UniqueConstraint(name: 'event_name_index', columns: ['event_name'])]
#[ORM\Entity(repositoryClass: ThrottlingRepository::class)]
class Throttling implements TestableInterface {
    #[ORM\Column(type: 'string', nullable: false)]
    private string $event_name;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $last_occurrence;

    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private int|string $id;

    public function getId(): int|string {
        return intval($this->id);
    }

    public function setId(int|string $new_id): void {
        $this->id = $new_id;
    }

    public function getEventName(): string {
        return $this->event_name;
    }

    public function setEventName(string $new_event_name): void {
        $this->event_name = $new_event_name;
    }

    public function getLastOccurrence(): ?\DateTime {
        return $this->last_occurrence;
    }

    public function setLastOccurrence(?\DateTime $new_last_occurrence): void {
        $this->last_occurrence = $new_last_occurrence;
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
