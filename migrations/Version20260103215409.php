<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260103215409 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add sport type to anniversary run';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE anniversary_runs ADD sport_type VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE anniversary_runs DROP sport_type');
    }
}
