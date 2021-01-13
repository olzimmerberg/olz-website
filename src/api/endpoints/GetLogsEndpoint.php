<?php

require_once __DIR__.'/../common/Endpoint.php';
require_once __DIR__.'/../common/HttpError.php';
require_once __DIR__.'/../../fields/EnumField.php';
require_once __DIR__.'/../../fields/IntegerField.php';
require_once __DIR__.'/../../fields/StringField.php';

class GetLogsEndpoint extends Endpoint {
    public static function getIdent() {
        return 'GetLogsEndpoint';
    }

    public function getResponseFields() {
        return [
            new StringField('content'),
        ];
    }

    public function getRequestFields() {
        return [
            new EnumField('logType', ['allowed_values' => [
                'ACCESS',
                'ERROR',
            ]]),
            new IntegerField('index', [
                'default_value' => 0,
                'min_value' => 0,
            ]),
        ];
    }

    protected function handle($input) {
        global $data_path;

        if ($this->session->get('auth') != 'all') {
            throw new HttpError(403, "Kein Zugriff!");
        }

        require_once __DIR__.'/../../config/paths.php';

        $logs_path = null;
        $local_logs_path = $data_path.'logs/';
        $remote_logs_path = $data_path.'../logs/';
        if (is_dir($local_logs_path)) {
            $logs_path = $local_logs_path;
        } elseif (is_dir($remote_logs_path)) {
            $logs_path = $remote_logs_path;
        }
        if ($logs_path == null) {
            throw new Exception("Keine Logs gefunden!");
        }

        $filename_by_log_type = [
            'ACCESS' => 'access',
            'ERROR' => 'error',
        ];
        $filename = $filename_by_log_type[$input['logType']];
        $index_suffix = $input['index'] == 0 ? '' : '.'.$input['index'];
        $uncompressed_filename = "{$local_logs_path}{$filename}.log{$index_suffix}";
        if (is_file($uncompressed_filename)) {
            return [
                'content' => file_get_contents($uncompressed_filename),
            ];
        }
        $compressed_filename = "{$local_logs_path}{$filename}.log{$index_suffix}.gz";
        if (is_file($compressed_filename)) {
            $content = '';
            $fp = gzopen($compressed_filename, 'r');
            do {
                $new_content = gzread($fp, 1000);
                if ($new_content) {
                    $content .= $new_content;
                }
            } while ($new_content);
            gzclose($fp);
            return [
                'content' => $content,
            ];
        }

        return [
            'content' => $uncompressed_filename."<br>".json_encode(scandir($logs_path)),
        ];
    }
}
