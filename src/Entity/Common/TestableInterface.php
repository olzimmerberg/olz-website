<?php

namespace Olz\Entity\Common;

interface TestableInterface {
    public function testOnlyGetField(string $field_name): mixed;
}
