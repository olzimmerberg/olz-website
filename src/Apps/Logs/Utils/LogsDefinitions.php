<?php

namespace Olz\Apps\Logs\Utils;

class LogsDefinitions {
    public static function getLogsChannels() {
        return [
            new OlzLogsChannel(),
            new AccessSslLogsChannel(),
        ];
    }
}
