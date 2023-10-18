<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231018165331 extends AbstractMigration {
    public function getDescription(): string {
        return 'Remove old image columns from news';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE news DROP bild1, DROP bild1_breite, DROP bild1_text, DROP bild2, DROP bild2_breite, DROP bild3, DROP bild3_breite, DROP zeit');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE news ADD bild1 LONGTEXT DEFAULT NULL, ADD bild1_breite INT DEFAULT NULL, ADD bild1_text LONGTEXT DEFAULT NULL, ADD bild2 LONGTEXT DEFAULT NULL, ADD bild2_breite INT DEFAULT NULL, ADD bild3 LONGTEXT DEFAULT NULL, ADD bild3_breite INT DEFAULT NULL, ADD zeit TIME DEFAULT NULL');
    }
}
