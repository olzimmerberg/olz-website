<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\WithUtilsCache;
use Olz\Tests\UnitTests\Common\UnitTestCase;

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\WithUtilsCache
 */
final class WithUtilsCacheTest extends UnitTestCase {
    public function testCanSetAndGetAllUtils(): void {
        $all_utils = ['testUtils' => 'test'];
        WithUtilsCache::setAll($all_utils);
        $this->assertSame($all_utils, WithUtilsCache::getAll());
    }

    public function testCanSetAndGetSpecificUtils(): void {
        $all_utils = ['testUtils' => 'test'];
        WithUtilsCache::setAll($all_utils);
        WithUtilsCache::set('anotherUtil', 'another');
        $this->assertSame('another', WithUtilsCache::get('anotherUtil'));
        $this->assertSame('test', WithUtilsCache::get('testUtils'));
        $this->assertSame([
            'testUtils' => 'test',
            'anotherUtil' => 'another',
        ], WithUtilsCache::getAll());
    }

    public function testCanResetCache(): void {
        $all_utils = ['testUtils' => 'test'];
        WithUtilsCache::setAll($all_utils);
        WithUtilsCache::reset();
        $this->assertSame([], WithUtilsCache::getAll());
    }
}
