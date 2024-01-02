<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240102172229 extends AbstractMigration {
    public function getDescription(): string {
        return 'Remove useless uniqueness constraint, which is causing issues';
    }

    public function up(Schema $schema): void {
        $this->addSql('DROP INDEX person_run_unique ON solv_results');
    }

    public function down(Schema $schema): void {
        $this->addSql('CREATE UNIQUE INDEX person_run_unique ON solv_results (person, event, class, name, birth_year, domicile, club)');
    }
}
