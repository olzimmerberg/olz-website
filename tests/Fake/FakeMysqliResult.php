<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

class FakeMysqliResult extends \mysqli_result {
    protected \mysqli $mysql;
    protected int $result_mode;

    public function __construct(\mysqli $mysql, int $result_mode = MYSQLI_STORE_RESULT) {
        $this->mysql = $mysql;
        $this->result_mode = $result_mode;
    }

    public function fetch_assoc(): array|false|null {
        return null;
    }
}
