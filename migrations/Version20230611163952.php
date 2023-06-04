<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230611163952 extends AbstractMigration {
    public function getDescription(): string {
        return 'Termin Bilder, Locations, Infos';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE termin_infos (id BIGINT AUTO_INCREMENT NOT NULL, termin_id INT NOT NULL, language VARCHAR(7) DEFAULT NULL, `index` INT NOT NULL, name LONGTEXT NOT NULL, content LONGTEXT DEFAULT NULL, INDEX IDX_E39736B2CA0B7C00 (termin_id), INDEX termin_language_index (termin_id, language, `index`), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE termin_locations (id INT AUTO_INCREMENT NOT NULL, owner_user_id INT DEFAULT NULL, owner_role_id INT DEFAULT NULL, created_by_user_id INT DEFAULT NULL, last_modified_by_user_id INT DEFAULT NULL, name VARCHAR(127) NOT NULL, details LONGTEXT DEFAULT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, image_ids LONGTEXT DEFAULT NULL, on_off INT DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_DA22EA1D2B18554A (owner_user_id), INDEX IDX_DA22EA1D5A75A473 (owner_role_id), INDEX IDX_DA22EA1D7D182D95 (created_by_user_id), INDEX IDX_DA22EA1D1A04EF5A (last_modified_by_user_id), INDEX name_index (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE termin_infos ADD CONSTRAINT FK_E39736B2CA0B7C00 FOREIGN KEY (termin_id) REFERENCES termine (id)');
        $this->addSql('ALTER TABLE termin_locations ADD CONSTRAINT FK_DA22EA1D2B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE termin_locations ADD CONSTRAINT FK_DA22EA1D5A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE termin_locations ADD CONSTRAINT FK_DA22EA1D7D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE termin_locations ADD CONSTRAINT FK_DA22EA1D1A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE termine ADD location_id INT DEFAULT NULL, ADD image_ids LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE termine ADD CONSTRAINT FK_168C0A8F64D218E FOREIGN KEY (location_id) REFERENCES termin_locations (id)');
        $this->addSql('CREATE INDEX IDX_168C0A8F64D218E ON termine (location_id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE termine DROP FOREIGN KEY FK_168C0A8F64D218E');
        $this->addSql('ALTER TABLE termin_infos DROP FOREIGN KEY FK_E39736B2CA0B7C00');
        $this->addSql('ALTER TABLE termin_locations DROP FOREIGN KEY FK_DA22EA1D2B18554A');
        $this->addSql('ALTER TABLE termin_locations DROP FOREIGN KEY FK_DA22EA1D5A75A473');
        $this->addSql('ALTER TABLE termin_locations DROP FOREIGN KEY FK_DA22EA1D7D182D95');
        $this->addSql('ALTER TABLE termin_locations DROP FOREIGN KEY FK_DA22EA1D1A04EF5A');
        $this->addSql('DROP TABLE termin_infos');
        $this->addSql('DROP TABLE termin_locations');
        $this->addSql('DROP INDEX IDX_168C0A8F64D218E ON termine');
        $this->addSql('ALTER TABLE termine DROP location_id, DROP image_ids');
    }
}
