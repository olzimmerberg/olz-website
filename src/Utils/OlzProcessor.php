<?php

namespace Olz\Utils;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class OlzProcessor implements ProcessorInterface {
    use WithUtilsTrait;
    public const UTILS = [
        'server',
        'session',
    ];

    public function __invoke(LogRecord $record): LogRecord {
        if ($this->server()) {
            $record->extra['url'] = $this->server()['REQUEST_URI'] ?? null;
            $record->extra['referrer'] = $this->server()['HTTP_REFERER'] ?? null;
            $record->extra['user_agent'] = $this->server()['HTTP_USER_AGENT'] ?? null;
        }
        if ($this->session()) {
            $record->extra['user'] = $this->session()->get('user');
            $record->extra['auth_user'] = $this->session()->get('auth_user');
        }
        return $record;
    }
}
