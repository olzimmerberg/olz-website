<?php

namespace Olz\Utils;

use PhpTypeScriptApi\PhpStan\PhpStanUtils;

/**
 * @template-covariant T of array
 */
class HttpParams {
    public PhpStanUtils $phpStanUtils;

    public function __construct() {
        $this->phpStanUtils = new PhpStanUtils();
    }

    public function configure(): void {
        // Do nothing by default
    }
}
