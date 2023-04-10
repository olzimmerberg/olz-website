<?php

namespace Olz\Components\Common;

use Olz\Utils\WithUtilsTrait;

abstract class OlzComponent {
    use WithUtilsTrait;

    public static function render($args = []): string {
        $class_name = get_called_class();
        $instance = new $class_name();
        return $instance->getHtml($args);
    }

    abstract public function getHtml($args = []): string;
}
