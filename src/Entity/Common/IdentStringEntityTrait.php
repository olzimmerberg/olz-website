<?php

namespace Olz\Entity\Common;

use Doctrine\ORM\Mapping as ORM;
use Olz\Utils\WithUtilsTrait;

/**
 * @phpstan-require-implements IdentStringEntityInterface
 */
trait IdentStringEntityTrait {
    use WithUtilsTrait;

    #[ORM\Column(type: 'string', length: 63, nullable: false)]
    protected string $ident;

    #[ORM\Column(type: 'string', length: 63, nullable: true)]
    protected ?string $old_ident;

    public function getIdent(): string {
        return $this->ident;
    }

    public function setIdent(string $new_value): void {
        $this->ident = $new_value;
    }

    public function getOldIdent(): string {
        return $this->old_ident;
    }

    public function setOldIdent(string $new_value): void {
        $this->old_ident = $new_value;
    }

    public function updateIdent(string $new_value): void {
        $truncated_new_value = substr($new_value, 0, 63);
        if ($truncated_new_value === $this->ident) {
            return;
        }
        $this->setOldIdent($this->ident);
        $this->setIdent($truncated_new_value);
    }
}
