<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261105194303 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add measurements';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE measurements (name VARCHAR(31) NOT NULL, year INT NOT NULL, month INT NOT NULL, value DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY (name, year, month)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void {
        $this->addSql('DROP TABLE measurements');
    }
}
