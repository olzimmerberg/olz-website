<?php

declare(strict_types=1);

namespace OLZ\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210822133337 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add guide to role, add image_ids to news';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE aktuell ADD image_ids LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE roles ADD guide LONGTEXT NOT NULL COMMENT \'restricted access\', CHANGE description description LONGTEXT NOT NULL COMMENT \'public\'');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE aktuell DROP image_ids');
        $this->addSql('ALTER TABLE roles DROP guide, CHANGE description description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
