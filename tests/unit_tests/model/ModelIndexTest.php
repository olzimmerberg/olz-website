<?php

declare(strict_types=1);

require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @coversNothing
 */
final class ModelIndexTest extends UnitTestCase {
    public function testAllModelsImportedInEachModuleIndex(): void {
        global $doctrine_model_folders;
        require_once __DIR__.'/../../../_/config/doctrine.php';
        $this->assertGreaterThan(0, count($doctrine_model_folders));

        foreach ($doctrine_model_folders as $model_path) {
            $model_files = scandir($model_path);
            $model_files = array_filter(
                $model_files,
                function ($filename) use ($model_path) {
                    if (preg_match('/Repository\.php$/', $filename)) {
                        return true;
                    }
                    if (!preg_match('/\.php$/', $filename)) {
                        return false;
                    }
                    $content = file_get_contents("{$model_path}/{$filename}");
                    if (!preg_match('/@ORM\\\\Entity/', $content)) {
                        return false;
                    }
                    return true;
                },
            );
            $this->assertGreaterThan(0, count($model_files));
            $index_content = file_get_contents("{$model_path}/index.php");
            foreach ($model_files as $model_file) {
                $pattern = '/require_once __DIR__\\.\'\/'.preg_quote($model_file).'\';/';
                $res = preg_match($pattern, $index_content, $matches);
                $this->assertSame(1, $res, "Missing import for {$model_file} in public/_/model/index.php");
            }
        }
    }

    public function testAllModulesImportedInMainIndex(): void {
        global $doctrine_model_folders;
        require_once __DIR__.'/../../../_/config/doctrine.php';
        $this->assertGreaterThan(0, count($doctrine_model_folders));

        $src_path = __DIR__.'/../../../_/';
        $main_model_path = __DIR__.'/../../../_/model/';
        $this->assertTrue(is_dir($src_path));
        $this->assertTrue(is_dir($main_model_path));
        $src_realpath = realpath($src_path);
        $main_model_realpath = realpath($main_model_path);
        $index_of_main = array_search($main_model_realpath, $doctrine_model_folders);
        $main_index_content = file_get_contents("{$main_model_realpath}/index.php");
        $this->assertNotTrue($index_of_main === false);
        foreach ($doctrine_model_folders as $index => $model_path) {
            if ($index === $index_of_main) {
                continue;
            }
            $model_path_from_src = substr($model_path, strlen($src_realpath));
            $model_path_pattern = str_replace('/', '\/', preg_quote($model_path_from_src));
            $pattern = '/require_once __DIR__\\.\'\/\\.\\.'.$model_path_pattern.'\/index\\.php\';/';
            $res = preg_match($pattern, $main_index_content, $matches);
            $this->assertSame(1, $res, "Missing import for {$model_path_from_src} in public/_/model/index.php");
        }
    }

    public function testAllModulesListedInDoctrine(): void {
        global $doctrine_model_folders;
        require_once __DIR__.'/../../../_/config/doctrine.php';
        $this->assertGreaterThan(0, count($doctrine_model_folders));

        $src_path = __DIR__.'/../../../_/';
        $this->assertTrue(is_dir($src_path));
        $src_realpath = realpath($src_path);
        $model_folder_indexes = glob("{$src_realpath}/*/model/index.php");
        foreach ($model_folder_indexes as $model_folder_index) {
            $res = preg_match('/^(.+)\/index\\.php$/', $model_folder_index, $matches);
            $this->assertSame(1, $res, "Model folder index expected to end in index.php...");
            $model_folder = $matches[1];
            $is_listed = array_search($model_folder, $doctrine_model_folders) !== false;
            $this->assertTrue($is_listed, "Model folder not listed in doctrine: {$model_folder}");
        }
    }
}
