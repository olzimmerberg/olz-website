<?php

declare(strict_types=1);

namespace OLZ\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210405231205 extends AbstractMigration {
    public function getDescription(): string {
        return 'Remove autoincrement constraint on olz_text';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE olz_text CHANGE id id INT NOT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE olz_text CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
