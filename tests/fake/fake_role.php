<?php

require_once __DIR__.'/../../public/_/model/Role.php';

function get_fake_role() {
    $role = new Role();
    $role->setId(1);
    return $role;
}
