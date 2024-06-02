<?php

namespace Olz\Components\Common;

use Olz\Utils\WithUtilsTrait;

abstract class OlzComponent {
    use WithUtilsTrait;

    /** @param array<string, mixed> $args */
    public static function render(array $args = [], mixed $caller = null): string {
        $class_name = get_called_class();
        $instance = new $class_name();
        if ($caller) {
            $instance->setAllUtils($caller->getAllUtils());
        }
        return $instance->getHtml($args);
    }

    /** @param array<string, mixed> $args */
    abstract public function getHtml(array $args = []): string;
}
