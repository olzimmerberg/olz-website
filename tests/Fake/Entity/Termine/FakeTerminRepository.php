<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

class FakeTerminRepository extends FakeOlzRepository {
    public $fakeOlzEntityClass = FakeTermin::class;

    public function findOneBy($where) {
        if ($where == ['solv_uid' => 12, 'on_off' => 1]) {
            return null;
        }
        if ($where == ['solv_uid' => 123, 'on_off' => 1]) {
            return FakeTermin::empty();
        }
        if ($where == ['solv_uid' => 1234, 'on_off' => 1]) {
            return FakeTermin::maximal();
        }
        return parent::findOneBy($where);
    }
}
