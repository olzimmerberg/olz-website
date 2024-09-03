<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240903121355 extends AbstractMigration {
    public function getDescription(): string {
        return 'Ident strings';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE karten ADD equidistance VARCHAR(255) DEFAULT NULL, ADD ident VARCHAR(63) NOT NULL AFTER id, ADD old_ident VARCHAR(63) DEFAULT NULL AFTER ident');
        $this->addSql('ALTER TABLE news ADD ident VARCHAR(63) NOT NULL AFTER id, ADD old_ident VARCHAR(63) DEFAULT NULL AFTER ident');
        $this->addSql('ALTER TABLE questions ADD old_ident VARCHAR(63) DEFAULT NULL AFTER ident, CHANGE ident ident VARCHAR(63) NOT NULL AFTER id');
        $this->addSql('ALTER TABLE termin_locations ADD ident VARCHAR(63) NOT NULL AFTER id, ADD old_ident VARCHAR(63) DEFAULT NULL AFTER ident');
        $this->addSql('ALTER TABLE termine ADD ident VARCHAR(63) NOT NULL AFTER id, ADD old_ident VARCHAR(63) DEFAULT NULL AFTER ident');

        $this->addSql("UPDATE karten SET ident=SUBSTR({$this->convertToIdent('name')}, 1, 63)");
        $this->addSql("UPDATE news SET ident=SUBSTR(CONCAT(DATE_FORMAT(published_date, '%Y-%m-%d_'), {$this->convertToIdent('title')}), 1, 63)");
        $this->addSql("UPDATE questions SET ident=SUBSTR({$this->convertToIdent('question')}, 1, 63)");
        $this->addSql("UPDATE termin_locations SET ident=SUBSTR({$this->convertToIdent('name')}, 1, 63)");
        $this->addSql("UPDATE termine SET ident=SUBSTR(CONCAT(DATE_FORMAT(start_date, '%Y-%m-%d_'), {$this->convertToIdent('title')}), 1, 63)");
    }

    protected function convertToIdent(string $field_name): string {
        return "REGEXP_REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LCASE({$field_name}), 'ä', 'ae'), 'ö', 'oe'), 'ü', 'ue'), ' ', '_'), '__', '_'), '[^a-z0-9_-]', '')";
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE karten DROP equidistance, DROP ident, DROP old_ident');
        $this->addSql('ALTER TABLE news DROP ident, DROP old_ident');
        $this->addSql('ALTER TABLE questions DROP old_ident, CHANGE ident ident VARCHAR(31) NOT NULL');
        $this->addSql('ALTER TABLE termine DROP ident, DROP old_ident');
        $this->addSql('ALTER TABLE termin_locations DROP ident, DROP old_ident');
    }
}
