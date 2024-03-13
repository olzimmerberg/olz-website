<?php

namespace Olz\Utils;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class AuthProcessor implements ProcessorInterface {
    use WithUtilsTrait;
    public const UTILS = [
        'session',
    ];

    public function __invoke(LogRecord $record): LogRecord {
        if (!$this->session()) {
            return $record;
        }

        $record->extra['user'] = $this->session()->get('user');
        $record->extra['auth_user'] = $this->session()->get('auth_user');

        return $record;
    }
}
