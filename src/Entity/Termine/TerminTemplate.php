<?php

namespace Olz\Entity\Termine;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\DataStorageInterface;
use Olz\Entity\Common\DataStorageTrait;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\SearchableInterface;
use Olz\Repository\Termine\TerminTemplateRepository;

#[ORM\Table(name: 'termin_templates')]
#[ORM\Entity(repositoryClass: TerminTemplateRepository::class)]
class TerminTemplate extends OlzEntity implements SearchableInterface, DataStorageInterface {
    use DataStorageTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'time', nullable: true)]
    private $start_time;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $duration_seconds;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $deadline_earlier_seconds;

    #[ORM\Column(type: 'time', nullable: true)]
    private $deadline_time;

    // TODO: Participants registration template

    #[ORM\Column(type: 'integer', nullable: true)]
    private $min_participants;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $max_participants;

    // TODO: Volunteers registration template

    #[ORM\Column(type: 'integer', nullable: true)]
    private $min_volunteers;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $max_volunteers;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    private $newsletter;

    #[ORM\Column(type: 'text', nullable: true)]
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private $text;

    // @deprecated Use labels
    #[ORM\Column(type: 'string', nullable: true)]
    private $types;

    #[ORM\JoinTable(name: 'termin_template_label_map')]
    #[ORM\JoinColumn(name: 'termin_template_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'label_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: TerminLabel::class, inversedBy: 'termin_templates')]
    private $labels;

    #[ORM\ManyToOne(targetEntity: TerminLocation::class)]
    #[ORM\JoinColumn(name: 'location_id', referencedColumnName: 'id', nullable: true)]
    private $location;

    #[ORM\Column(type: 'text', nullable: true)]
    private $image_ids;

    public function __construct() {
        $this->labels = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function setId($new_value) {
        $this->id = $new_value;
    }

    public function getStartTime() {
        return $this->start_time;
    }

    public function setStartTime($new_value) {
        $this->start_time = $new_value;
    }

    public function getDurationSeconds() {
        return $this->duration_seconds;
    }

    public function setDurationSeconds($new_value) {
        $this->duration_seconds = $new_value;
    }

    public function getDeadlineEarlierSeconds() {
        return $this->deadline_earlier_seconds;
    }

    public function setDeadlineEarlierSeconds($new_value) {
        $this->deadline_earlier_seconds = $new_value;
    }

    public function getDeadlineTime() {
        return $this->deadline_time;
    }

    public function setDeadlineTime($new_value) {
        $this->deadline_time = $new_value;
    }

    public function getMinParticipants() {
        return $this->min_participants;
    }

    public function setMinParticipants($new_value) {
        $this->min_participants = $new_value;
    }

    public function getMaxParticipants() {
        return $this->max_participants;
    }

    public function setMaxParticipants($new_value) {
        $this->max_participants = $new_value;
    }

    public function getMinVolunteers() {
        return $this->min_volunteers;
    }

    public function setMinVolunteers($new_value) {
        $this->min_volunteers = $new_value;
    }

    public function getMaxVolunteers() {
        return $this->max_volunteers;
    }

    public function setMaxVolunteers($new_value) {
        $this->max_volunteers = $new_value;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($new_value) {
        $this->title = $new_value;
    }

    public function getText() {
        return $this->text;
    }

    public function setText($new_value) {
        $this->text = $new_value;
    }

    public function getTypes() {
        return $this->types;
    }

    public function setTypes($new_value) {
        $this->types = $new_value;
    }

    public function getLabels() {
        return $this->labels;
    }

    public function addLabel(TerminLabel $label) {
        $this->labels->add($label);
    }

    public function removeLabel(TerminLabel $label) {
        $this->labels->removeElement($label);
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

    // ---

    public static function getIdFieldNameForSearch(): string {
        return 'id';
    }

    public function getIdForSearch(): int {
        return $this->getId();
    }

    public static function getCriteriaForQuery(string $query): Expression {
        return Criteria::expr()->orX(
            Criteria::expr()->contains('title', $query),
        );
    }

    public function getTitleForSearch(): string {
        return $this->getTitle();
    }

    public static function getEntityNameForStorage(): string {
        return 'termin_templates';
    }

    public function getEntityIdForStorage(): string {
        return "{$this->getId()}";
    }
}
