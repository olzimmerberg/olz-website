<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260530162734 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add latency measurement';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE counter ADD latency_avg_ms DOUBLE PRECISION NOT NULL, ADD latency_num INT NOT NULL, DROP args, CHANGE page page VARCHAR(255) NOT NULL, CHANGE counter counter INT NOT NULL, CHANGE date_range date_range VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE counter ADD args LONGTEXT DEFAULT NULL, DROP latency_avg_ms, DROP latency_num, CHANGE page page VARCHAR(255) DEFAULT NULL, CHANGE date_range date_range VARCHAR(255) DEFAULT NULL, CHANGE counter counter INT DEFAULT NULL');
    }
}
