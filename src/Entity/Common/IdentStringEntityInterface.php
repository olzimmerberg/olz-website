<?php

namespace Olz\Entity\Common;

interface IdentStringEntityInterface {
    public function getIdent(): string;

    public function setIdent(string $new_value): void;

    public function getOldIdent(): string;

    public function setOldIdent(string $new_value): void;

    public function updateIdent(string $new_value): void;
}
