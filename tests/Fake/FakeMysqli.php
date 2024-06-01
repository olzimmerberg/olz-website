<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Utils\WithUtilsTrait;

class FakeMysqli extends \mysqli {
    use WithUtilsTrait;

    public function query(string $query, int $result_mode = MYSQLI_STORE_RESULT): \mysqli_result {
        $this->log()->info("DB: {$query}");
        return new FakeMysqliResult($this, $result_mode);
    }

    public function multi_query(string $sql): bool {
        $this->log()->info("DB: {$sql}");
        return false;
    }
}
