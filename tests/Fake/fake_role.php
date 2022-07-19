<?php

use Olz\Entity\Role;

function get_fake_role() {
    $role = new Role();
    $role->setId(1);
    return $role;
}
