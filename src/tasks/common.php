<?php

require_once __DIR__.'/../config/paths.php';

abstract class BackgroundTask {
    private $log_path;
    private $log_file;

    private function setup() {
        $this->log_path = $this->generate_log_path();
        if (!is_dir(dirname($this->log_path))) {
            mkdir(dirname($this->log_path), 0777, true);
        }
        $this->log_file = fopen($this->log_path, 'a');

        ignore_user_abort(true);
        ob_end_clean();
        header("Connection: close");
        ignore_user_abort(true);
        ob_start();
        echo 'Task will run in the background. Bye.';
        $size = ob_get_length();
        header("Content-Length: {$size}");
        ob_end_flush();
        flush();
        ob_end_clean();
        if (session_id()) {
            session_write_close();
        }
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    private function teardown() {
        fclose($this->log_file);
    }

    // public function __construct() {
    // }

    public function run() {
        $this->setup();
        try {
            $this->run_specific_task();
        } catch (Exception $exc) {
        } finally {
            $this->teardown();
        }
    }

    protected function generate_log_path() {
        global $data_path;
        $timestamp = date('Y-m-d_H_i_s');
        return "{$data_path}tasks/log_{$timestamp}_{$this->get_ident()}.txt";
    }

    abstract protected static function get_ident();

    abstract protected function run_specific_task();

    protected function log_info($message) {
        $timestamp = date('Y-m-d H:i:s');
        $this->log_line("[ INFO] {$timestamp} {$message}");
    }

    protected function log_warning($message) {
        $timestamp = date('Y-m-d H:i:s');
        $this->log_line("[ WARN] {$timestamp} {$message}");
    }

    protected function log_error($message) {
        $timestamp = date('Y-m-d H:i:s');
        $this->log_line("[ERROR] {$timestamp} {$message}");
    }

    protected function log_line($line) {
        fwrite($this->log_file, "{$line}\n");
        fflush($this->log_file);
    }
}
