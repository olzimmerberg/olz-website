<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240313195047 extends AbstractMigration {
    public function getDescription(): string {
        return 'Make Role an OlzEntity';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE roles ADD owner_user_id INT DEFAULT NULL, ADD owner_role_id INT DEFAULT NULL, ADD created_by_user_id INT DEFAULT NULL, ADD last_modified_by_user_id INT DEFAULT NULL, ADD on_off INT DEFAULT 1 NOT NULL, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE roles ADD CONSTRAINT FK_B63E2EC72B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE roles ADD CONSTRAINT FK_B63E2EC75A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE roles ADD CONSTRAINT FK_B63E2EC77D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE roles ADD CONSTRAINT FK_B63E2EC71A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_B63E2EC72B18554A ON roles (owner_user_id)');
        $this->addSql('CREATE INDEX IDX_B63E2EC75A75A473 ON roles (owner_role_id)');
        $this->addSql('CREATE INDEX IDX_B63E2EC77D182D95 ON roles (created_by_user_id)');
        $this->addSql('CREATE INDEX IDX_B63E2EC71A04EF5A ON roles (last_modified_by_user_id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE roles DROP FOREIGN KEY FK_B63E2EC72B18554A');
        $this->addSql('ALTER TABLE roles DROP FOREIGN KEY FK_B63E2EC75A75A473');
        $this->addSql('ALTER TABLE roles DROP FOREIGN KEY FK_B63E2EC77D182D95');
        $this->addSql('ALTER TABLE roles DROP FOREIGN KEY FK_B63E2EC71A04EF5A');
        $this->addSql('DROP INDEX IDX_B63E2EC72B18554A ON roles');
        $this->addSql('DROP INDEX IDX_B63E2EC75A75A473 ON roles');
        $this->addSql('DROP INDEX IDX_B63E2EC77D182D95 ON roles');
        $this->addSql('DROP INDEX IDX_B63E2EC71A04EF5A ON roles');
        $this->addSql('ALTER TABLE roles DROP owner_user_id, DROP owner_role_id, DROP created_by_user_id, DROP last_modified_by_user_id, DROP on_off, DROP created_at, DROP last_modified_at');
    }
}
