<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230918214135 extends AbstractMigration {
    public function getDescription(): string {
        return 'Remove deprecated, migrated fields';
    }

    public function up(Schema $schema): void {
        $this->addSql('DROP INDEX datum_index ON aktuell');
        $this->addSql('ALTER TABLE aktuell DROP datum, DROP titel, DROP text, DROP textlang, DROP link, DROP autor, DROP typ, DROP autor_email');
        $this->addSql('DROP INDEX datum_on_off_index ON termine');
        $this->addSql('ALTER TABLE termine DROP datum, DROP datum_end, DROP datum_off, DROP zeit, DROP zeit_end, DROP titel, DROP solv_event_link, DROP ical_uid');
        $this->addSql('CREATE INDEX start_date_on_off_index ON termine (start_date, on_off)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE aktuell ADD datum DATE NOT NULL, ADD titel LONGTEXT DEFAULT NULL, ADD text LONGTEXT DEFAULT NULL, ADD textlang LONGTEXT DEFAULT NULL, ADD link LONGTEXT DEFAULT NULL, ADD autor VARCHAR(255) DEFAULT NULL, ADD typ LONGTEXT NOT NULL, ADD autor_email VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE INDEX datum_index ON aktuell (datum)');
        $this->addSql('DROP INDEX start_date_on_off_index ON termine');
        $this->addSql('ALTER TABLE termine ADD datum DATE DEFAULT NULL, ADD datum_end DATE DEFAULT NULL, ADD datum_off DATE DEFAULT NULL, ADD zeit TIME DEFAULT \'00:00:00\', ADD zeit_end TIME DEFAULT \'00:00:00\', ADD titel LONGTEXT DEFAULT NULL, ADD solv_event_link LONGTEXT DEFAULT NULL, ADD ical_uid VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE INDEX datum_on_off_index ON termine (datum, on_off)');
    }
}
