<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

trait FakeFetcherTrait {
    protected function getMockedResponse(
        string $request_ident,
        string $directory,
        ?callable $get_suggestion_fn = null
    ): bool|string {
        $called_class = get_called_class();
        $file_path = $directory."/data/{$called_class}/{$request_ident}.txt";
        if (is_file($file_path)) {
            return file_get_contents($file_path);
        }
        if (preg_match('/inexistent/', $file_path)) {
            throw new \Exception("Unmocked request: {$request_ident}");
        }
        if ($get_suggestion_fn === null) {
            throw new \Exception("Unmocked request: {$request_ident}; no suggestion.");
        }
        $real_data = $get_suggestion_fn();
        $suggested_file_path =
            $directory."/data/{$called_class}/{$request_ident}.suggestion.txt";
        if (!is_dir(dirname($suggested_file_path))) {
            mkdir(dirname($suggested_file_path), 0o777, true);
        }
        file_put_contents($suggested_file_path, $real_data);
        throw new \Exception("Unmocked request: {$request_ident}; Suggestion written to {$suggested_file_path}.");
    }
}
