<?php

class FakeTask {
    public $hasBeenRun = false;

    public function run() {
        $this->hasBeenRun = true;
    }
}
