<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220601201046 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migrate doctrine to symfony';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE forum CHANGE allowHTML allow_html INT DEFAULT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE forum CHANGE allow_html allowHTML INT DEFAULT NULL');
    }
}
