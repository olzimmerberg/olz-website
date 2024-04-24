<?php

namespace Olz\Utils;

class DbUtils {
    use WithUtilsTrait;

    protected static $db;
    protected static $entityManager;

    public function getDb() {
        if (self::$db !== null) {
            return self::$db;
        }

        $db = new \mysqli(
            $this->envUtils()->getMysqlServer(),
            $this->envUtils()->getMysqlUsername(),
            $this->envUtils()->getMysqlPassword(),
            $this->envUtils()->getMysqlSchema()
        );

        if ($db->connect_error) {
            throw new \Exception("MySQL Connect Error ({$db->connect_errno}) {$db->connect_error}");
        }

        $db->set_charset('utf8mb4');
        $db->query("SET NAMES utf8mb4");
        $db->query("SET time_zone = '+00:00';");

        self::$db = $db;
        return $db;
    }

    public function getEntityManager() {
        if (self::$entityManager !== null) {
            return self::$entityManager;
        }

        global $kernel, $entityManager;

        if ($kernel && !isset($entityManager)) {
            $entityManager = $kernel->getContainer()->get('doctrine.orm.default_entity_manager');
        }

        self::$entityManager = $entityManager;
        return $entityManager;
    }

    public static function fromEnv(): self {
        return new self();
    }
}
