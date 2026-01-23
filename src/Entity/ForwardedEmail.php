<?php

namespace Olz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\TestableInterface;
use Olz\Entity\Users\User;
use Olz\Repository\ForwardedEmailRepository;

#[ORM\Table(name: 'forwarded_emails')]
#[ORM\Index(name: 'recipient_user_id_forwarded_at_index', columns: ['recipient_user_id', 'forwarded_at'])]
#[ORM\Entity(repositoryClass: ForwardedEmailRepository::class)]
class ForwardedEmail implements TestableInterface {
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'recipient_user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $recipient_user;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $sender_address;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $subject;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $body;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $forwarded_at;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $error_message;

    #[ORM\Id]
    #[ORM\Column(type: 'bigint', nullable: false)]
    #[ORM\GeneratedValue]
    private int|string $id;

    public function getId(): ?int {
        return isset($this->id) ? intval($this->id) : null;
    }

    public function setId(int $new_id): void {
        $this->id = $new_id;
    }

    public function getRecipientUser(): ?User {
        return $this->recipient_user;
    }

    public function setRecipientUser(?User $new_value): void {
        $this->recipient_user = $new_value;
    }

    public function getSenderAddress(): string {
        return $this->sender_address;
    }

    public function setSenderAddress(string $new_value): void {
        $this->sender_address = $new_value;
    }

    public function getSubject(): string {
        return $this->subject;
    }

    public function setSubject(string $new_value): void {
        $this->subject = $new_value;
    }

    public function getBody(): string {
        return $this->body;
    }

    public function setBody(string $new_value): void {
        $this->body = $new_value;
    }

    public function getForwardedAt(): ?\DateTime {
        return $this->forwarded_at;
    }

    public function setForwardedAt(?\DateTime $new_value): void {
        $this->forwarded_at = $new_value;
    }

    public function getErrorMessage(): ?string {
        return $this->error_message;
    }

    public function setErrorMessage(?string $new_value): void {
        $this->error_message = $new_value;
    }

    // ---

    public function testOnlyGetField(string $field_name): mixed {
        return $this->{$field_name};
    }
}
