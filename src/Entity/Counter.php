<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\TestableInterface;
use Olz\Repository\CounterRepository;

#[ORM\Table(name: 'counter')]
#[ORM\Index(name: 'date_range_page_index', columns: ['date_range', 'page'])]
#[ORM\Entity(repositoryClass: CounterRepository::class)]
class Counter implements TestableInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private int|string $id;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $page;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $date_range;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $args;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $counter;
    // PRIMARY KEY (`id`)

    public function getId(): ?int {
        return isset($this->id) ? intval($this->id) : null;
    }

    public function setId(int $new_id): void {
        $this->id = $new_id;
    }

    public function getPage(): ?string {
        return $this->page;
    }

    public function setPage(?string $new_page): void {
        $this->page = $new_page;
    }

    public function getDateRange(): ?string {
        return $this->date_range;
    }

    public function setDateRange(?string $new_date_range): void {
        $this->date_range = $new_date_range;
    }

    public function getArgs(): ?string {
        return $this->args;
    }

    public function setArgs(?string $new_args): void {
        $this->args = $new_args;
    }

    public function getCounter(): ?int {
        return $this->counter;
    }

    public function setCounter(?int $new_counter): void {
        $this->counter = $new_counter;
    }

    public function incrementCounter(): void {
        $this->setCounter($this->getCounter() + 1);
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
