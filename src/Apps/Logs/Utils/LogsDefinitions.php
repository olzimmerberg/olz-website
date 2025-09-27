<?php

namespace Olz\Apps\Logs\Utils;

class LogsDefinitions {
    /** @return array<BaseLogsChannel> */
    public static function getLogsChannels(): array {
        return [
            new OlzLogsChannel(),
            new ErrorLogsChannel(),
            new AccessSslLogsChannel(),
        ];
    }
}
