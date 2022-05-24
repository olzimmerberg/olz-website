<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200511211417 extends AbstractMigration {
    public function getDescription(): string {
        return 'Drop old user & vorstand tables';
    }

    public function up(Schema $schema): void {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vorstand');
        $this->addSql('DROP TABLE vorstand_funktion');
    }

    public function down(Schema $schema): void {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, benutzername VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8_general_ci`, passwort VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8_general_ci`, zugriff TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8_general_ci`, root TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE vorstand (id INT AUTO_INCREMENT NOT NULL, name TINYTEXT CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, funktion TINYTEXT CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci` COMMENT \'alt\', adresse TEXT CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, tel TINYTEXT CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, email TINYTEXT CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, bild TINYTEXT CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`, on_off INT DEFAULT 1 NOT NULL, position INT DEFAULT NULL COMMENT \'alt\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE vorstand_funktion (id INT AUTO_INCREMENT NOT NULL, vorstand INT NOT NULL, funktion INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
    }
}
