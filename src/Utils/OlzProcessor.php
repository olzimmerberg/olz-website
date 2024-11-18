<?php

namespace Olz\Utils;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class OlzProcessor implements ProcessorInterface {
    use WithUtilsTrait;

    public function __invoke(LogRecord $record): LogRecord {
        if ($this->server()) {
            $record->extra['url'] = $this->protectTokens($this->server()['REQUEST_URI'] ?? null);
            $record->extra['referrer'] = $this->server()['HTTP_REFERER'] ?? null;
            $record->extra['user_agent'] = $this->server()['HTTP_USER_AGENT'] ?? null;
        }
        $record->extra['user'] = $this->session()->get('user');
        $record->extra['auth_user'] = $this->session()->get('auth_user');
        $trace = debug_backtrace();
        $general_utils = new GeneralUtils();
        $trace_overview = $general_utils->getTraceOverview($trace);
        return $record->with(channel: $trace_overview);
    }

    protected function protectTokens(?string $unsanitized): ?string {
        if (!$unsanitized) {
            return $unsanitized;
        }
        return preg_replace('/(access\_token\=[a-zA-Z0-9\_\-\+\/]{3})[a-zA-Z0-9\_\-\+\/]*([a-zA-Z0-9\_\-\+\/]{3})/', '$1***$2', $unsanitized);
    }
}
