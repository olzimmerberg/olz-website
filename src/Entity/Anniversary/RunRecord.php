<?php

namespace Olz\Entity\Anniversary;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\TestableInterface;
use Olz\Entity\Users\User;
use Olz\Repository\Anniversary\RunRecordRepository;

#[ORM\Table(name: 'anniversary_runs')]
#[ORM\Index(name: 'run_at_index', columns: ['run_at'])]
#[ORM\Index(name: 'source_index', columns: ['source'])]
#[ORM\Entity(repositoryClass: RunRecordRepository::class)]
class RunRecord extends OlzEntity implements TestableInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private int|string $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $user;

    #[ORM\Column(type: 'string', nullable: false, options: ['default' => ''])]
    private string $runner_name;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $run_at;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $is_counting;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $distance_meters;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $elevation_meters;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $source;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $info;

    public function getId(): ?int {
        return isset($this->id) ? intval($this->id) : null;
    }

    public function setId(int $new_id): void {
        $this->id = $new_id;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $new_user): void {
        $this->user = $new_user;
    }

    public function getRunnerName(): string {
        return $this->runner_name;
    }

    public function setRunnerName(string $new_value): void {
        $this->runner_name = $new_value;
    }

    public function getRunAt(): \DateTime {
        return $this->run_at;
    }

    public function setRunAt(\DateTime $new_value): void {
        $this->run_at = $new_value;
    }

    public function getIsCounting(): bool {
        return $this->is_counting;
    }

    public function setIsCounting(bool $new_value): void {
        $this->is_counting = $new_value;
    }

    public function getDistanceMeters(): int {
        return $this->distance_meters;
    }

    public function setDistanceMeters(int $new_value): void {
        $this->distance_meters = $new_value;
    }

    public function getElevationMeters(): int {
        return $this->elevation_meters;
    }

    public function setElevationMeters(int $new_value): void {
        $this->elevation_meters = $new_value;
    }

    public function getSource(): ?string {
        return $this->source;
    }

    public function setSource(?string $new_value): void {
        $this->source = $new_value;
    }

    public function getInfo(): ?string {
        return $this->info;
    }

    public function setInfo(?string $new_value): void {
        $this->info = $new_value;
    }

    // ---

    public function __toString(): string {
        $id = $this->getId();
        return "RunRecord (ID: {$id})";
    }

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
