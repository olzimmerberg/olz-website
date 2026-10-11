<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261011134507 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add sessions table to store sessions in the database';
    }

    public function up(Schema $schema): void {
        $this->addSql(
            'CREATE TABLE `sessions` ('
            .'`sess_id` VARBINARY(128) NOT NULL PRIMARY KEY, '
            .'`sess_data` BLOB NOT NULL, '
            .'`sess_lifetime` INTEGER UNSIGNED NOT NULL, '
            .'`sess_time` INTEGER UNSIGNED NOT NULL, '
            .'INDEX `sess_lifetime_idx` (`sess_lifetime`)'
            .') COLLATE utf8mb4_bin, ENGINE = InnoDB'
        );
    }

    public function down(Schema $schema): void {
        $this->addSql('DROP TABLE `sessions`');
    }
}
