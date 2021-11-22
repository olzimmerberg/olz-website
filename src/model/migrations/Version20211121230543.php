<?php

declare(strict_types=1);

namespace OLZ\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211121230543 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add running tasks table';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE running_tasks (id VARCHAR(255) NOT NULL, task_class VARCHAR(255) NOT NULL, is_currently_executing TINYINT(1) DEFAULT \'0\' NOT NULL, state LONGTEXT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void {
        $this->addSql('DROP TABLE running_tasks');
    }
}
