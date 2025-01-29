<?php

namespace Olz\Components\Common;

use Olz\Utils\WithUtilsTrait;

/**
 * @template T
 */
abstract class OlzComponent {
    use WithUtilsTrait;

    /** @param T $args */
    public static function render(mixed $args = [], mixed $caller = null): string {
        $class_name = get_called_class();
        $instance = new $class_name();
        if ($caller) {
            $instance->setAllUtils($caller->getAllUtils());
        }
        return $instance->getHtml($args);
    }

    /** @param T $args */
    abstract public function getHtml(mixed $args): string;
}
