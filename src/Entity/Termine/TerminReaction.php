<?php

namespace Olz\Entity\Termine;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\TestableInterface;
use Olz\Entity\Users\User;
use Olz\Repository\Termine\TerminReactionRepository;

#[ORM\Table(name: 'termin_reactions')]
#[ORM\Index(name: 'termin_emoji_user_index', columns: ['termin_id', 'emoji', 'user_id'])]
#[ORM\Entity(repositoryClass: TerminReactionRepository::class)]
class TerminReaction implements TestableInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private int|string $id;

    #[ORM\ManyToOne(targetEntity: Termin::class)]
    #[ORM\JoinColumn(name: 'termin_id', referencedColumnName: 'id', nullable: false)]
    private Termin $termin;

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

    public function getTermin(): Termin {
        return $this->termin;
    }

    public function setTermin(Termin $new_value): void {
        $this->termin = $new_value;
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
