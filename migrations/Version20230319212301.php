<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230319212301 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add panini infos';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE panini24 ADD infos LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE panini24 DROP infos');
    }
}
