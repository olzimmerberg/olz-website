<?php

declare(strict_types=1);

require_once __DIR__.'/../common/UnitTestCase.php';

/**
 * @internal
 * @coversNothing
 */
final class ModelIndexTest extends UnitTestCase {
    public function testAllModelsInIndex(): void {
        $model_path = __DIR__.'/../../../src/model/';
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
                $content = file_get_contents($model_path.$filename);
                if (!preg_match('/@ORM\\\\Entity/', $content)) {
                    return false;
                }
                return true;
            },
        );
        $this->assertGreaterThan(0, count($model_files));
        $index_content = file_get_contents($model_path.'index.php');
        foreach ($model_files as $model_file) {
            $pattern = '/require_once __DIR__\\.\'\/'.preg_quote($model_file).'\';/';
            $res = preg_match($pattern, $index_content, $matches);
            $this->assertSame(1, $res, "Missing import for {$model_file} in src/model/index.php");
        }
    }
}
