<?php

namespace Olz\Entity\News;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\TestableInterface;
use Olz\Entity\Users\User;
use Olz\Repository\News\NewsReactionRepository;

#[ORM\Table(name: 'news_reactions')]
#[ORM\Index(name: 'news_emoji_user_index', columns: ['news_entry_id', 'emoji', 'user_id'])]
#[ORM\Entity(repositoryClass: NewsReactionRepository::class)]
class NewsReaction implements TestableInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private int|string $id;

    #[ORM\ManyToOne(targetEntity: NewsEntry::class)]
    #[ORM\JoinColumn(name: 'news_entry_id', referencedColumnName: 'id', nullable: false)]
    private NewsEntry $news_entry;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[ORM\Column(type: 'string', length: 15, nullable: false)]
    private string $emoji;

    public function getId(): ?int {
        return isset($this->id) ? intval($this->id) : null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
    }

    public function getNewsEntry(): NewsEntry {
        return $this->news_entry;
    }

    public function setNewsEntry(NewsEntry $new_value): void {
        $this->news_entry = $new_value;
    }

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $new_value): void {
        $this->user = $new_value;
    }

    public function getEmoji(): string {
        return $this->emoji;
    }

    public function setEmoji(string $new_value): void {
        $this->emoji = $new_value;
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
