<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251231090437 extends AbstractMigration {
    public function getDescription(): string {
        return 'Improve anniversary evaluation';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE anniversary_runs ADD runner_name VARCHAR(255) DEFAULT \'\' NOT NULL, ADD is_counting TINYINT DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE anniversary_runs DROP runner_name, DROP is_counting');
    }
}
