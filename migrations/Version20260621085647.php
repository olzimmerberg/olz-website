<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260621085647 extends AbstractMigration {
    public function getDescription(): string {
        return 'Fix emoji collation';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE news_reactions CHANGE emoji emoji VARCHAR(15) NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE termin_reactions CHANGE emoji emoji VARCHAR(15) NOT NULL COLLATE `utf8mb4_bin`');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE news_reactions CHANGE emoji emoji VARCHAR(15) NOT NULL');
        $this->addSql('ALTER TABLE termin_reactions CHANGE emoji emoji VARCHAR(15) NOT NULL');
    }
}
