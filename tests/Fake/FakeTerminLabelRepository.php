<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

class FakeTerminLabelRepository extends FakeOlzRepository {
    public $fakeOlzEntityClass = FakeTerminLabels::class;

    public $terminLabelToBeFound;
    public $terminLabelToBeFoundForQuery;

    public function findOneBy($where) {
        if ($this->terminLabelToBeFound !== null) {
            return $this->terminLabelToBeFound;
        }
        if ($this->terminLabelToBeFoundForQuery !== null) {
            $fn = $this->terminLabelToBeFoundForQuery;
            return $fn($where);
        }
        return parent::findOneBy($where);
    }
}
