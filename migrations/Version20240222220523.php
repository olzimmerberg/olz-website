<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240222220523 extends AbstractMigration {
    public function getDescription(): string {
        return 'Karten update';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE karten ADD owner_user_id INT DEFAULT NULL, ADD owner_role_id INT DEFAULT NULL, ADD created_by_user_id INT DEFAULT NULL, ADD last_modified_by_user_id INT DEFAULT NULL, ADD on_off INT DEFAULT 1 NOT NULL, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL, CHANGE position position INT NOT NULL, CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE karten ADD CONSTRAINT FK_57ED7BE12B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE karten ADD CONSTRAINT FK_57ED7BE15A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE karten ADD CONSTRAINT FK_57ED7BE17D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE karten ADD CONSTRAINT FK_57ED7BE11A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_57ED7BE12B18554A ON karten (owner_user_id)');
        $this->addSql('CREATE INDEX IDX_57ED7BE15A75A473 ON karten (owner_role_id)');
        $this->addSql('CREATE INDEX IDX_57ED7BE17D182D95 ON karten (created_by_user_id)');
        $this->addSql('CREATE INDEX IDX_57ED7BE11A04EF5A ON karten (last_modified_by_user_id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE karten DROP FOREIGN KEY FK_57ED7BE12B18554A');
        $this->addSql('ALTER TABLE karten DROP FOREIGN KEY FK_57ED7BE15A75A473');
        $this->addSql('ALTER TABLE karten DROP FOREIGN KEY FK_57ED7BE17D182D95');
        $this->addSql('ALTER TABLE karten DROP FOREIGN KEY FK_57ED7BE11A04EF5A');
        $this->addSql('DROP INDEX IDX_57ED7BE12B18554A ON karten');
        $this->addSql('DROP INDEX IDX_57ED7BE15A75A473 ON karten');
        $this->addSql('DROP INDEX IDX_57ED7BE17D182D95 ON karten');
        $this->addSql('DROP INDEX IDX_57ED7BE11A04EF5A ON karten');
        $this->addSql('ALTER TABLE karten DROP owner_user_id, DROP owner_role_id, DROP created_by_user_id, DROP last_modified_by_user_id, DROP on_off, DROP created_at, DROP last_modified_at, DROP latitude, DROP longitude, CHANGE position position INT DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL');
    }
}
