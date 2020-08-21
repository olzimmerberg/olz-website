<?php

abstract class AbstractSession {
    abstract public function has($key);

    abstract public function get($key);

    abstract public function set($key, $new_value);

    abstract public function delete($key);
}
