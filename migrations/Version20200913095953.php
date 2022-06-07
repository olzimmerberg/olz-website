<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200913095953 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add AuthRequest';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE auth_requests (id BIGINT AUTO_INCREMENT NOT NULL, ip_address VARCHAR(40) NOT NULL, timestamp DATETIME DEFAULT NULL, action VARCHAR(31) NOT NULL, username LONGTEXT NOT NULL, INDEX ip_address_timestamp_index (ip_address, timestamp), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void {
        $this->addSql('DROP TABLE auth_requests');
    }
}
