<?php

namespace Olz\Entity\Members;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Common\TestableInterface;
use Olz\Entity\Users\User;
use Olz\Repository\Members\MemberRepository;

#[ORM\Table(name: 'members')]
#[ORM\Index(name: 'ident_index', columns: ['ident'])]
#[ORM\Index(name: 'user_id_index', columns: ['user_id'])]
#[ORM\Entity(repositoryClass: MemberRepository::class)]
class Member extends OlzEntity implements TestableInterface {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    public int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $user;

    #[ORM\Column(type: 'string', nullable: false)]
    public string $ident;

    #[ORM\Column(type: 'text', nullable: false)]
    public string $data;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $updates;

    public function getId(): ?int {
        return $this->id ?? null;
    }

    public function setId(int $new_value): void {
        $this->id = $new_value;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $new_user): void {
        $this->user = $new_user;
    }

    public function getIdent(): string {
        return $this->ident;
    }

    public function setIdent(string $new_value): void {
        $this->ident = $new_value;
    }

    public function getData(): string {
        return $this->data;
    }

    public function setData(string $new_value): void {
        $this->data = $new_value;
    }

    public function getUpdates(): ?string {
        return $this->updates;
    }

    public function setUpdates(?string $new_value): void {
        $this->updates = $new_value;
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
