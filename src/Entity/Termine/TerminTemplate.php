<?php

namespace Olz\Entity\Termine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\DataStorageInterface;
use Olz\Entity\Common\DataStorageTrait;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\SearchableInterface;
use Olz\Entity\Common\TestableInterface;
use Olz\Entity\Users\User;
use Olz\Repository\Termine\TerminTemplateRepository;

#[ORM\Table(name: 'termin_templates')]
#[ORM\Entity(repositoryClass: TerminTemplateRepository::class)]
class TerminTemplate extends OlzEntity implements DataStorageInterface, SearchableInterface, TestableInterface {
    use DataStorageTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTime $start_time;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $duration_seconds;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $deadline_earlier_seconds;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTime $deadline_time;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    private bool $should_promote;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'organizer_user_id', referencedColumnName: 'id', nullable: true)]
    protected ?User $organizer_user;

    // TODO: Participants registration template

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $min_participants;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $max_participants;

    // TODO: Volunteers registration template

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $min_volunteers;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $max_volunteers;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    private bool $newsletter;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $text;

    /** @var Collection<int|string, TerminLabel>&iterable<TerminLabel> */
    #[ORM\JoinTable(name: 'termin_template_label_map')]
    #[ORM\JoinColumn(name: 'termin_template_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'label_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: TerminLabel::class, inversedBy: 'termin_templates')]
    private Collection $labels;

    #[ORM\ManyToOne(targetEntity: TerminLocation::class)]
    #[ORM\JoinColumn(name: 'location_id', referencedColumnName: 'id', nullable: true)]
    private ?TerminLocation $location;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $image_ids;

    public function __construct() {
        $this->labels = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
    }

    public function getStartTime(): ?\DateTime {
        return $this->start_time;
    }

    public function setStartTime(?\DateTime $new_value): void {
        $this->start_time = $new_value;
    }

    public function getDurationSeconds(): ?int {
        return $this->duration_seconds;
    }

    public function setDurationSeconds(?int $new_value): void {
        $this->duration_seconds = $new_value;
    }

    public function getDeadlineEarlierSeconds(): ?int {
        return $this->deadline_earlier_seconds;
    }

    public function setDeadlineEarlierSeconds(?int $new_value): void {
        $this->deadline_earlier_seconds = $new_value;
    }

    public function getDeadlineTime(): ?\DateTime {
        return $this->deadline_time;
    }

    public function setDeadlineTime(?\DateTime $new_value): void {
        $this->deadline_time = $new_value;
    }

    public function getShouldPromote(): bool {
        return $this->should_promote;
    }

    public function setShouldPromote(bool $new_value): void {
        $this->should_promote = $new_value;
    }

    public function getOrganizerUser(): ?User {
        return $this->organizer_user;
    }

    public function setOrganizerUser(?User $new_value): void {
        $this->organizer_user = $new_value;
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

    // ---

    public function __toString(): string {
        return "TerminTemplate (ID: {$this->getId()})";
    }

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }

    public static function getEntityNameForStorage(): string {
        return 'termin_templates';
    }

    public function getEntityIdForStorage(): string {
        return "{$this->getId()}";
    }

    public static function getIdFieldNameForSearch(): string {
        return 'id';
    }

    public function getIdForSearch(): int {
        return $this->getId() ?? 0;
    }

    public function getTitleForSearch(): string {
        return $this->getTitle() ?? '';
    }

    public static function getCriteriaForFilter(string $key, string $value): Expression {
        throw new \Exception("No such TerminTemplate filter: {$key}");
    }

    public static function getCriteriaForQuery(string $query): Expression {
        return Criteria::expr()->orX(
            Criteria::expr()->contains('title', $query),
        );
    }
}
