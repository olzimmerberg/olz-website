<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210411185009 extends AbstractMigration {
    public function getDescription(): string {
        return 'Update counter';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE counter ADD date_range VARCHAR(255) DEFAULT NULL, DROP counter_ip, DROP start_date, DROP end_date, DROP counter_bak, DROP counter_ip_bak, DROP bak_date, CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE page page VARCHAR(255) DEFAULT NULL, CHANGE name args LONGTEXT DEFAULT NULL');
        $this->addSql('CREATE INDEX date_range_page_index ON counter (date_range, page)');
    }

    public function down(Schema $schema): void {
        $this->addSql('DROP INDEX date_range_page_index ON counter');
        $this->addSql('ALTER TABLE counter ADD counter_ip INT DEFAULT NULL, ADD start_date DATE DEFAULT NULL, ADD end_date DATE DEFAULT NULL, ADD counter_bak INT DEFAULT NULL, ADD counter_ip_bak INT DEFAULT NULL, ADD bak_date DATE DEFAULT NULL, DROP date_range, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE page page LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE args name LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
