<?php

namespace Olz\Entity\News;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\DataStorageInterface;
use Olz\Entity\Common\DataStorageTrait;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Roles\Role;
use Olz\Entity\User;
use Olz\Repository\News\NewsRepository;

#[ORM\Table(name: 'news')]
#[ORM\Index(name: 'published_index', columns: ['published_date', 'published_time'])]
#[ORM\Entity(repositoryClass: NewsRepository::class)]
class NewsEntry extends OlzEntity implements DataStorageInterface {
    use DataStorageTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $termin;

    #[ORM\Column(type: 'date', nullable: false)]
    private \DateTime $published_date;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTime $published_time;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 1])]
    private bool $newsletter;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $teaser;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $content;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $image_ids;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $external_url;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $author_name;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $author_email;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'author_user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $author_user;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(name: 'author_role_id', referencedColumnName: 'id', nullable: true)]
    private ?Role $author_role;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $format;

    #[ORM\Column(type: 'text', nullable: false, options: ['default' => ''])]
    private string $tags;

    #[ORM\Column(type: 'integer', nullable: false, options: ['default' => 0])]
    private int $counter;
    // PRIMARY KEY (`id`),
    // KEY `datum` (`datum`)

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
    }

    public function getPublishedDate(): \DateTime {
        return $this->published_date;
    }

    public function setPublishedDate(\DateTime $new_value): void {
        $this->published_date = $new_value;
    }

    public function getPublishedTime(): ?\DateTime {
        return $this->published_time;
    }

    public function setPublishedTime(?\DateTime $new_value): void {
        $this->published_time = $new_value;
    }

    public function getFormat(): string {
        return $this->format;
    }

    public function setFormat(string $new_value): void {
        $this->format = $new_value;
    }

    public function getAuthorName(): ?string {
        return $this->author_name;
    }

    public function setAuthorName(?string $new_value): void {
        $this->author_name = $new_value;
    }

    public function getAuthorEmail(): ?string {
        return $this->author_email;
    }

    public function setAuthorEmail(?string $new_value): void {
        $this->author_email = $new_value;
    }

    public function getAuthorUser(): ?User {
        return $this->author_user;
    }

    public function setAuthorUser(?User $new_value): void {
        $this->author_user = $new_value;
    }

    public function getAuthorRole(): ?Role {
        return $this->author_role;
    }

    public function setAuthorRole(?Role $new_value): void {
        $this->author_role = $new_value;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $new_value): void {
        $this->title = $new_value;
    }

    public function getTeaser(): ?string {
        return $this->teaser;
    }

    public function setTeaser(?string $new_value): void {
        $this->teaser = $new_value;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(?string $new_value): void {
        $this->content = $new_value;
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

    public function getTags(): string {
        return $this->tags;
    }

    public function setTags(string $new_value): void {
        $this->tags = $new_value;
    }

    public function getExternalUrl(): ?string {
        return $this->external_url;
    }

    public function setExternalUrl(?string $new_value): void {
        $this->external_url = $new_value;
    }

    public function getTermin(): int {
        return $this->termin;
    }

    public function setTermin(int $new_value): void {
        $this->termin = $new_value;
    }

    public function getCounter(): int {
        return $this->counter;
    }

    public function setCounter(int $new_value): void {
        $this->counter = $new_value;
    }

    /** @deprecated */
    public function getNewsletter(): bool {
        return $this->newsletter;
    }

    /** @deprecated */
    public function setNewsletter(bool $new_value): void {
        $this->newsletter = $new_value;
    }

    public static function getEntityNameForStorage(): string {
        return 'news';
    }

    public function getEntityIdForStorage(): string {
        return "{$this->getId()}";
    }
}
