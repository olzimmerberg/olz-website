<?php

namespace Olz\Entity\Termine;

use Doctrine\ORM\Mapping as ORM;
use Olz\Repository\Termine\TerminInfoRepository;

#[ORM\Table(name: 'termin_infos')]
#[ORM\Index(name: 'termin_language_index', columns: ['termin_id', 'language', 'index'])]
#[ORM\Entity(repositoryClass: TerminInfoRepository::class)]
class TerminInfo {
    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\ManyToOne(targetEntity: Termin::class)]
    #[ORM\JoinColumn(name: 'termin_id', referencedColumnName: 'id', nullable: false)]
    private $termin;

    #[ORM\Column(type: 'string', length: 7, nullable: true)]
    private $language;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $index;

    #[ORM\Column(type: 'text', nullable: false)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $content;

    public function getId() {
        return $this->id;
    }

    public function setId($new_value) {
        $this->id = $new_value;
    }

    public function getTermin() {
        return $this->termin;
    }

    public function setTermin(Termin $new_value) {
        $this->termin = $new_value;
    }

    public function getLanguage() {
        return $this->language;
    }

    public function setLanguage($new_value) {
        $this->language = $new_value;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($new_value) {
        $this->name = $new_value;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($new_value) {
        $this->content = $new_value;
    }
}
