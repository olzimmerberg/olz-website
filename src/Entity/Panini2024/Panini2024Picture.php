<?php

namespace Olz\Entity\Panini2024;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\TestableInterface;
use Olz\Repository\Panini2024\Panini2024PictureRepository;

#[ORM\Table(name: 'panini24')]
#[ORM\Entity(repositoryClass: Panini2024PictureRepository::class)]
class Panini2024Picture extends OlzEntity implements TestableInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private int|string $id;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $line1;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $line2;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $association;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $img_src;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $img_style;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $is_landscape;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $has_top;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTime $birthdate;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $num_mispunches;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $infos;

    public function getId(): ?int {
        return isset($this->id) ? intval($this->id) : null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
    }

    public function getLine1(): string {
        return $this->line1;
    }

    public function setLine1(string $new_value): void {
        $this->line1 = $new_value;
    }

    public function getLine2(): ?string {
        return $this->line2;
    }

    public function setLine2(?string $new_value): void {
        $this->line2 = $new_value;
    }

    public function getAssociation(): ?string {
        return $this->association;
    }

    public function setAssociation(?string $new_value): void {
        $this->association = $new_value;
    }

    public function getImgSrc(): string {
        return $this->img_src;
    }

    public function setImgSrc(string $new_value): void {
        $this->img_src = $new_value;
    }

    public function getImgStyle(): string {
        return $this->img_style;
    }

    public function setImgStyle(string $new_value): void {
        $this->img_style = $new_value;
    }

    public function getIsLandscape(): bool {
        return $this->is_landscape;
    }

    public function setIsLandscape(bool $new_value): void {
        $this->is_landscape = $new_value;
    }

    public function getHasTop(): bool {
        return $this->has_top;
    }

    public function setHasTop(bool $new_value): void {
        $this->has_top = $new_value;
    }

    public function getBirthdate(): ?\DateTime {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTime $new_value): void {
        $this->birthdate = $new_value;
    }

    public function getNumMispunches(): ?int {
        return $this->num_mispunches;
    }

    public function setNumMispunches(?int $new_value): void {
        $this->num_mispunches = $new_value;
    }

    /** @return array<string> */
    public function getInfos(): array {
        $array = json_decode($this->infos ?? '[]', true);
        if (!is_array($array)) {
            return [];
        }
        $strings = [];
        foreach ($array as $string) {
            if (!is_string($string)) {
                return [];
            }
            $strings[] = $string;
        }
        return $strings;
    }

    /** @param array<string> $new_value */
    public function setInfos(array $new_value): void {
        $sane_value = json_encode($new_value);
        if (!$sane_value) {
            return;
        }
        $this->infos = $sane_value;
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
