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

    #[ORM\Column(type: 'string', nullable: false)]
    private string $page;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $date_range;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $counter;

    #[ORM\Column(type: 'float', nullable: false)]
    private float $latency_avg_ms;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $latency_num;
    // PRIMARY KEY (`id`)

    public function getId(): ?int {
        return isset($this->id) ? intval($this->id) : null;
    }

    public function setId(int $new_id): void {
        $this->id = $new_id;
    }

    public function getPage(): string {
        return $this->page;
    }

    public function setPage(string $new_page): void {
        $this->page = $new_page;
    }

    public function getDateRange(): string {
        return $this->date_range;
    }

    public function setDateRange(string $new_date_range): void {
        $this->date_range = $new_date_range;
    }

    public function getCounter(): int {
        return $this->counter;
    }

    public function setCounter(int $new_value): void {
        $this->counter = $new_value;
    }

    public function incrementCounter(): void {
        $this->setCounter($this->getCounter() + 1);
    }

    public function getLatencyAvgMs(): float {
        return $this->latency_avg_ms;
    }

    public function setLatencyAvgMs(float $new_value): void {
        $this->latency_avg_ms = $new_value;
    }

    public function getLatencyNum(): int {
        return $this->latency_num;
    }

    public function setLatencyNum(int $new_value): void {
        $this->latency_num = $new_value;
    }

    public function addLatencyMeasurment(float $new_latency_ms): void {
        $avg = $this->getLatencyAvgMs();
        $num = $this->getLatencyNum();
        $new_num = $num + 1;
        $new_avg = ($avg * $num + $new_latency_ms * 1) / $new_num;
        $this->setLatencyAvgMs($new_avg);
        $this->setLatencyNum($new_num);
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
