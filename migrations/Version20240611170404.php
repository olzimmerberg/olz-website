<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240611170404 extends AbstractMigration {
    public function getDescription(): string {
        return 'Remove CH04 coordinates';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE karten DROP center_x, DROP center_y');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE karten ADD center_x INT DEFAULT NULL, ADD center_y INT DEFAULT NULL');
    }
}
