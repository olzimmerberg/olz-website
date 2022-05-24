<?php

global $kernel, $entityManager;

if ($kernel && !isset($entityManager)) {
    $entityManager = $kernel->getContainer()->get('doctrine.orm.default_entity_manager');
}
