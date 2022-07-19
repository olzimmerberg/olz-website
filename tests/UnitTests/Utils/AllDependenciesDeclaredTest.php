<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsTrait;

// Accessing trait var WithUtilsTrait::$ALL_UTILS is deprecated.
class AllDependenciesDeclaredClassWithUtilsTrait {
    use WithUtilsTrait;
}

/**
 * @internal
 * @coversNothing
 */
final class AllDependenciesDeclaredTest extends UnitTestCase {
    public function testAllCommonUtilsCovered(): void {
        $src_path = __DIR__.'/../../../src/';
        $this->assertTrue(is_dir($src_path));
        $src_realpath = realpath($src_path);
        $utils_files = array_merge(
            glob("{$src_realpath}/Utils/*.php"),
            glob("{$src_realpath}/*/Utils/*.php"),
        );
        $util_paths = array_filter(
            $utils_files,
            function ($filename) {
                return
                    preg_match('/\.php$/', $filename)
                    && basename($filename) !== 'WithUtilsTrait.php'
                ;
            },
        );
        $this->assertGreaterThan(0, count($util_paths));
        foreach ($util_paths as $util_path) {
            require_once $util_path;
            $util_path_contents = file_get_contents($util_path);
            $res = preg_match('/\/src\/(.+)\.php$/', $util_path, $matches);
            $this->assertSame(1, $res);
            $class_name = '\\Olz\\'.str_replace('/', '\\', $matches[1]);
            $declared_dependencies = $class_name::UTILS;
            $all_utils = AllDependenciesDeclaredClassWithUtilsTrait::$ALL_UTILS;
            foreach ($all_utils as $util_name) {
                $util_name_esc = preg_quote($util_name);
                $is_used = strpos($util_path_contents, "\$this->{$util_name_esc}") !== false;
                $is_declared = array_search($util_name, $declared_dependencies) !== false;
                if ($is_used) {
                    $this->assertTrue(
                        $is_declared,
                        "{$util_name} is used but not declared in {$class_name}"
                    );
                } else {
                    $this->assertFalse(
                        $is_declared,
                        "{$util_name} is declared but not used in {$class_name}"
                    );
                }
            }
        }
    }
}
