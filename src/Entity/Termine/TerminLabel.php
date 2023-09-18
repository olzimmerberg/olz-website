<?php

namespace Olz\Entity\Termine;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\SearchableInterface;
use Olz\Repository\Termine\TerminLabelRepository;

#[ORM\Table(name: 'termin_labels')]
#[ORM\Index(name: 'name_index', columns: ['name'])]
#[ORM\Entity(repositoryClass: TerminLabelRepository::class)]
class TerminLabel extends OlzEntity implements SearchableInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'string', length: 127, nullable: false)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $details;

    #[ORM\Column(type: 'text', nullable: true)]
    private $icon;

    #[ORM\ManyToMany(targetEntity: Termin::class, mappedBy: 'labels')]
    private $termine;

    #[ORM\ManyToMany(targetEntity: TerminTemplate::class, mappedBy: 'labels')]
    private $termin_templates;

    public function getId() {
        return $this->id;
    }

    public function setId($new_value) {
        $this->id = $new_value;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($new_value) {
        $this->name = $new_value;
    }

    public function getDetails() {
        return $this->details;
    }

    public function setDetails($new_value) {
        $this->details = $new_value;
    }

    public function getIcon() {
        return $this->icon;
    }

    public function setIcon($new_value) {
        $this->icon = $new_value;
    }

    public static function getIdFieldNameForSearch(): string {
        return 'id';
    }

    public function getIdForSearch(): int {
        return $this->getId();
    }

    public static function getFieldNamesForSearch(): array {
        return ['name'];
    }

    public function getTitleForSearch(): string {
        return $this->getName();
    }
}
