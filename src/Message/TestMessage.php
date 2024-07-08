<?php

namespace Olz\Message;

class TestMessage {
    public function __construct(
        private string $ident = 'n/a',
    ) {
    }

    public function getIdent(): string {
        return $this->ident;
    }
}
