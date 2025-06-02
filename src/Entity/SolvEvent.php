<?php

namespace Olz\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\SearchableInterface;
use Olz\Entity\Common\TestableInterface;
use Olz\Repository\SolvEventRepository;
use Olz\Utils\WithUtilsTrait;

#[ORM\Table(name: 'solv_events')]
#[ORM\Index(name: 'date_index', columns: ['date'])]
#[ORM\Entity(repositoryClass: SolvEventRepository::class)]
class SolvEvent implements SearchableInterface, TestableInterface {
    use WithUtilsTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $solv_uid;

    #[ORM\Column(type: 'date', nullable: false)]
    private \DateTime $date;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $duration;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $kind;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $day_night;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $national;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $region;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $type;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $link;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $club;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $map;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $location;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $coord_x;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $coord_y;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTime $deadline;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $entryportal;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $start_link;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $rank_link;

    #[ORM\Column(type: 'datetime', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $last_modification;

    /** @var array<string, true> */
    private array $valid_field_names = [
        'solv_uid' => true,
        'date' => true,
        'duration' => true,
        'kind' => true,
        'day_night' => true,
        'national' => true,
        'region' => true,
        'type' => true,
        'name' => true,
        'link' => true,
        'club' => true,
        'map' => true,
        'location' => true,
        'coord_x' => true,
        'coord_y' => true,
        'deadline' => true,
        'entryportal' => true,
        'start_link' => true,
        'rank_link' => true,
        'last_modification' => true,
    ];
    // PRIMARY KEY (`solv_uid`)

    public function getSolvUid(): int {
        return $this->solv_uid;
    }

    public function setSolvUid(int $new_value): void {
        $this->solv_uid = $new_value;
    }

    public function getDate(): \DateTime {
        return $this->date;
    }

    public function setDate(\DateTime $new_value): void {
        $this->date = $new_value;
    }

    public function getDuration(): int {
        return $this->duration;
    }

    public function setDuration(int $new_value): void {
        $this->duration = $new_value;
    }

    public function getKind(): string {
        return $this->kind;
    }

    public function setKind(string $new_value): void {
        $this->kind = $new_value;
    }

    public function getDayNight(): string {
        return $this->day_night;
    }

    public function setDayNight(string $new_value): void {
        $this->day_night = $new_value;
    }

    public function getNational(): int {
        return $this->national;
    }

    public function setNational(int $new_value): void {
        $this->national = $new_value;
    }

    public function getRegion(): string {
        return $this->region;
    }

    public function setRegion(string $new_value): void {
        $this->region = $new_value;
    }

    public function getType(): string {
        return $this->type;
    }

    public function setType(string $new_value): void {
        $this->type = $new_value;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $new_value): void {
        $this->name = $new_value;
    }

    public function getLink(): string {
        return $this->link;
    }

    public function setLink(string $new_value): void {
        $this->link = $new_value;
    }

    public function getClub(): string {
        return $this->club;
    }

    public function setClub(string $new_value): void {
        $this->club = $new_value;
    }

    public function getMap(): string {
        return $this->map;
    }

    public function setMap(string $new_value): void {
        $this->map = $new_value;
    }

    public function getLocation(): string {
        return $this->location;
    }

    public function setLocation(string $new_value): void {
        $this->location = $new_value;
    }

    public function getCoordX(): int {
        return $this->coord_x;
    }

    public function setCoordX(int $new_value): void {
        $this->coord_x = $new_value;
    }

    public function getCoordY(): int {
        return $this->coord_y;
    }

    public function setCoordY(int $new_value): void {
        $this->coord_y = $new_value;
    }

    public function getDeadline(): ?\DateTime {
        return $this->deadline;
    }

    public function setDeadline(?\DateTime $new_value): void {
        $this->deadline = $new_value;
    }

    public function getEntryportal(): int {
        return $this->entryportal;
    }

    public function setEntryportal(int $new_value): void {
        $this->entryportal = $new_value;
    }

    public function getStartLink(): ?string {
        return $this->start_link;
    }

    public function setStartLink(?string $new_value): void {
        $this->start_link = $new_value;
    }

    public function getRankLink(): ?string {
        return $this->rank_link;
    }

    public function setRankLink(?string $new_value): void {
        $this->rank_link = $new_value;
    }

    public function getLastModification(): \DateTime {
        return $this->last_modification;
    }

    public function setLastModification(\DateTime $new_value): void {
        $this->last_modification = $new_value;
    }

    public function getFieldValue(string $field_name): mixed {
        if (!isset($this->valid_field_names[$field_name])) {
            throw new \Exception("getFieldValue: Invalid field name: {$field_name}", 1);
        }
        return $this->{$field_name};
    }

    public function setFieldValue(string $field_name, mixed $new_value): void {
        if (!isset($this->valid_field_names[$field_name])) {
            throw new \Exception("setFieldValue: Invalid field name: {$field_name}", 1);
        }
        $this->{$field_name} = $new_value;
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }

    public static function getIdFieldNameForSearch(): string {
        return 'solv_uid';
    }

    public function getIdForSearch(): int {
        return $this->getSolvUid();
    }

    public function getTitleForSearch(): string {
        $pretty_date = $this->getDate()->format('Y-m-d');
        return "{$pretty_date}: {$this->getName()}";
    }

    public static function getCriteriaForFilter(string $key, string $value): Expression {
        throw new \Exception("No such SolvEvent filter: {$key}");
    }

    public static function getCriteriaForQuery(string $query): Expression {
        if (preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', $query)) {
            return Criteria::expr()->orX(
                Criteria::expr()->eq('date', new \DateTime($query)),
                Criteria::expr()->contains('name', $query),
            );
        }
        if (intval($query) > 1900) {
            $this_year = strval(intval($query));
            $next_year = strval(intval($query) + 1);
            return Criteria::expr()->orX(
                Criteria::expr()->andX(
                    Criteria::expr()->gte('date', new \DateTime("{$this_year}-01-01 00:00:00")),
                    Criteria::expr()->lt('date', new \DateTime("{$next_year}-01-01 00:00:00")),
                ),
                Criteria::expr()->contains('name', $query),
            );
        }
        return Criteria::expr()->orX(
            Criteria::expr()->contains('name', $query),
        );
    }
}
