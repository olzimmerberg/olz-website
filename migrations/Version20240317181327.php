<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240317181327 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migrate to TerminLabel';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE downloads CHANGE position position INT NOT NULL');
        $this->addSql('CREATE INDEX position_index ON downloads (on_off, position)');
        $this->addSql('ALTER TABLE karten DROP position');
        $this->addSql('CREATE INDEX typ_index ON karten (on_off, typ)');
        $this->addSql('ALTER TABLE links CHANGE position position INT NOT NULL');
        $this->addSql('CREATE INDEX position_index ON links (on_off, position)');
        $this->addSql('ALTER TABLE termin_labels ADD ident VARCHAR(31) NOT NULL, ADD position INT NOT NULL');
        $this->addSql('CREATE INDEX ident_index ON termin_labels (on_off, ident)');
        $this->addSql('CREATE INDEX position_index ON termin_labels (on_off, position)');
    }

    public function down(Schema $schema): void {
        $this->addSql('DROP INDEX position_index ON downloads');
        $this->addSql('ALTER TABLE downloads CHANGE position position INT DEFAULT NULL');
        $this->addSql('DROP INDEX typ_index ON karten');
        $this->addSql('ALTER TABLE karten ADD position INT NOT NULL');
        $this->addSql('DROP INDEX position_index ON links');
        $this->addSql('ALTER TABLE links CHANGE position position INT DEFAULT NULL');
        $this->addSql('DROP INDEX ident_index ON termin_labels');
        $this->addSql('DROP INDEX position_index ON termin_labels');
        $this->addSql('ALTER TABLE termin_labels DROP ident, DROP position');
    }
}
