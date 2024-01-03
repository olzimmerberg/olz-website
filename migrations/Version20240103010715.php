<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240103010715 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add SOLV indexes';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE INDEX date_index ON solv_events (date)');
        $this->addSql('CREATE INDEX same_as_index ON solv_people (same_as)');
        $this->addSql('CREATE INDEX person_name_index ON solv_results (person, name)');
        $this->addSql('CREATE INDEX event_index ON solv_results (event)');
    }

    public function down(Schema $schema): void {
        $this->addSql('DROP INDEX date_index ON solv_events');
        $this->addSql('DROP INDEX same_as_index ON solv_people');
        $this->addSql('DROP INDEX person_name_index ON solv_results');
        $this->addSql('DROP INDEX event_index ON solv_results');
    }
}
