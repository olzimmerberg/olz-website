<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsTrait;

class WithUtilsTraitClassWithUtilsTrait {
    use WithUtilsTrait;
}

/**
 * @internal
 *
 * @covers \Olz\Utils\WithUtilsTrait
 */
final class WithUtilsTraitTest extends UnitTestCase {
    public function testCanSetAndGetAllUtils(): void {
        $all_utils = array_filter(
            WithUtilsTraitClassWithUtilsTrait::$ALL_UTILS,
            function ($util_name) {
                return $util_name !== 'log';
            }
        );
        $instance = new WithUtilsTraitClassWithUtilsTrait();
        $this->assertGreaterThan(0, count($all_utils));
        foreach ($all_utils as $util_name) {
            $cap_util_name = ucfirst($util_name);
            $setter_name = "set{$cap_util_name}";
            $instance->{$setter_name}($util_name);
            $this->assertSame($util_name, $instance->{$util_name}());
        }
    }
}
