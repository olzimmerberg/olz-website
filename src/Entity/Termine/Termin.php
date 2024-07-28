<?php

namespace Olz\Entity\Termine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Anmelden\Registration;
use Olz\Entity\Common\DataStorageInterface;
use Olz\Entity\Common\DataStorageTrait;
use Olz\Entity\Common\OlzEntity;
use Olz\Repository\Termine\TerminRepository;

#[ORM\Table(name: 'termine')]
#[ORM\Index(name: 'start_date_on_off_index', columns: ['start_date', 'on_off'])]
#[ORM\Entity(repositoryClass: TerminRepository::class)]
class Termin extends OlzEntity implements DataStorageInterface {
    use DataStorageTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'date', nullable: false)]
    private \DateTime $start_date;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTime $start_time;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTime $end_date;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTime $end_time;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $deadline;

    #[ORM\ManyToOne(targetEntity: Registration::class)]
    #[ORM\JoinColumn(name: 'participants_registration_id', referencedColumnName: 'id')]
    private ?Registration $participants_registration;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $num_participants;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $min_participants;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $max_participants;

    #[ORM\ManyToOne(targetEntity: Registration::class)]
    #[ORM\JoinColumn(name: 'volunteers_registration_id', referencedColumnName: 'id')]
    private ?Registration $volunteers_registration;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $num_volunteers;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $min_volunteers;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $max_volunteers;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    private bool $newsletter;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $go2ol;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $text;

    /** @var Collection<int|string, TerminLabel>&iterable<TerminLabel> */
    #[ORM\JoinTable(name: 'termin_label_map')]
    #[ORM\JoinColumn(name: 'termin_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'label_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: TerminLabel::class, inversedBy: 'termine')]
    private Collection $labels;

    #[ORM\ManyToOne(targetEntity: TerminLocation::class)]
    #[ORM\JoinColumn(name: 'location_id', referencedColumnName: 'id', nullable: true)]
    private ?TerminLocation $location;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $xkoord;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $ykoord;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $solv_uid;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $image_ids;

    // PRIMARY KEY (`id`),
    // KEY `datum` (`datum`),
    // KEY `on_off` (`on_off`),
    // KEY `datum_end` (`datum_end`),
    // KEY `datum_off` (`datum_off`)

    public function __construct() {
        $this->labels = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
    }

    public function getStartDate(): \DateTime {
        return $this->start_date;
    }

    public function setStartDate(\DateTime $new_value): void {
        $this->start_date = $new_value;
    }

    public function getStartTime(): ?\DateTime {
        return $this->start_time;
    }

    public function setStartTime(?\DateTime $new_value): void {
        $this->start_time = $new_value;
    }

    public function getEndDate(): ?\DateTime {
        return $this->end_date;
    }

    public function setEndDate(?\DateTime $new_value): void {
        $this->end_date = $new_value;
    }

    public function getEndTime(): ?\DateTime {
        return $this->end_time;
    }

    public function setEndTime(?\DateTime $new_value): void {
        $this->end_time = $new_value;
    }

    public function getDeadline(): ?\DateTime {
        return $this->deadline;
    }

    public function setDeadline(?\DateTime $new_value): void {
        $this->deadline = $new_value;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(?string $new_value): void {
        $this->title = $new_value;
    }

    public function getText(): ?string {
        return $this->text;
    }

    public function setText(?string $new_value): void {
        $this->text = $new_value;
    }

    /** @return Collection<int|string, TerminLabel>&iterable<TerminLabel> */
    public function getLabels(): Collection {
        return $this->labels;
    }

    public function addLabel(TerminLabel $label): void {
        $this->labels->add($label);
    }

    public function removeLabel(TerminLabel $label): void {
        $this->labels->removeElement($label);
    }

    public function clearLabels(): void {
        $this->labels->clear();
    }

    public function getSolvId(): ?int {
        return $this->solv_uid;
    }

    public function setSolvId(?int $new_value): void {
        $this->solv_uid = $new_value;
    }

    // @deprecated Use SolvId to get the go2ol (or other platform) ID
    public function getGo2olId(): ?string {
        return $this->go2ol;
    }

    // @deprecated Use SolvId to get the go2ol (or other platform) ID
    public function setGo2olId(?string $new_value): void {
        $this->go2ol = $new_value;
    }

    public function getCoordinateX(): ?int {
        return $this->xkoord;
    }

    public function setCoordinateX(?int $new_value): void {
        $this->xkoord = $new_value;
    }

    public function getCoordinateY(): ?int {
        return $this->ykoord;
    }

    public function setCoordinateY(?int $new_value): void {
        $this->ykoord = $new_value;
    }

    public function getLocation(): ?TerminLocation {
        return $this->location;
    }

    public function setLocation(?TerminLocation $new_value): void {
        $this->location = $new_value;
    }

    public function getNewsletter(): bool {
        return $this->newsletter;
    }

    public function setNewsletter(bool $new_value): void {
        $this->newsletter = $new_value;
    }

    /** @return array<string> */
    public function getImageIds(): array {
        if ($this->image_ids == null) {
            return [];
        }
        $array = json_decode($this->image_ids, true);
        return is_array($array) ? $array : [];
    }

    /** @param array<string> $new_value */
    public function setImageIds(array $new_value): void {
        $enc_value = json_encode($new_value);
        if (!$enc_value) {
            return;
        }
        $this->image_ids = $enc_value;
    }

    public function getParticipantsRegistration(): ?Registration {
        return $this->participants_registration;
    }

    public function setParticipantsRegistration(?Registration $new_value): void {
        $this->participants_registration = $new_value;
    }

    public function getNumParticipants(): ?int {
        return $this->num_participants;
    }

    public function setNumParticipants(?int $new_value): void {
        $this->num_participants = $new_value;
    }

    public function getMinParticipants(): ?int {
        return $this->min_participants;
    }

    public function setMinParticipants(?int $new_value): void {
        $this->min_participants = $new_value;
    }

    public function getMaxParticipants(): ?int {
        return $this->max_participants;
    }

    public function setMaxParticipants(?int $new_value): void {
        $this->max_participants = $new_value;
    }

    public function getVolunteersRegistration(): ?Registration {
        return $this->volunteers_registration;
    }

    public function setVolunteersRegistration(?Registration $new_value): void {
        $this->volunteers_registration = $new_value;
    }

    public function getNumVolunteers(): ?int {
        return $this->num_volunteers;
    }

    public function setNumVolunteers(?int $new_value): void {
        $this->num_volunteers = $new_value;
    }

    public function getMinVolunteers(): ?int {
        return $this->min_volunteers;
    }

    public function setMinVolunteers(?int $new_value): void {
        $this->min_volunteers = $new_value;
    }

    public function getMaxVolunteers(): ?int {
        return $this->max_volunteers;
    }

    public function setMaxVolunteers(?int $new_value): void {
        $this->max_volunteers = $new_value;
    }

    // ---

    public static function getEntityNameForStorage(): string {
        return 'termine';
    }

    public function getEntityIdForStorage(): string {
        return "{$this->getId()}";
    }
}
