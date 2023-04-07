<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Repository\CounterRepository;

#[ORM\Table(name: 'counter')]
#[ORM\Index(name: 'date_range_page_index', columns: ['date_range', 'page'])]
#[ORM\Entity(repositoryClass: CounterRepository::class)]
class Counter {
    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'string', nullable: true)]
    private $page;

    #[ORM\Column(type: 'string', nullable: true)]
    private $date_range;

    #[ORM\Column(type: 'text', nullable: true)]
    private $args;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $counter;
    // PRIMARY KEY (`id`)

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getPage() {
        return $this->page;
    }

    public function setPage($new_page) {
        $this->page = $new_page;
    }

    public function getDateRange() {
        return $this->date_range;
    }

    public function setDateRange($new_date_range) {
        $this->date_range = $new_date_range;
    }

    public function getArgs() {
        return $this->args;
    }

    public function setArgs($new_args) {
        $this->args = $new_args;
    }

    public function getCounter() {
        return $this->counter;
    }

    public function setCounter($new_counter) {
        $this->counter = $new_counter;
    }

    public function incrementCounter() {
        $this->setCounter($this->getCounter() + 1);
    }
}
