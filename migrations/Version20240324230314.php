<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240324230314 extends AbstractMigration {
    public function getDescription(): string {
        return 'Make olz_text an OlzEntity';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE olz_text ADD owner_user_id INT DEFAULT NULL, ADD owner_role_id INT DEFAULT NULL, ADD created_by_user_id INT DEFAULT NULL, ADD last_modified_by_user_id INT DEFAULT NULL, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE olz_text ADD CONSTRAINT FK_2ACA45892B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE olz_text ADD CONSTRAINT FK_2ACA45895A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE olz_text ADD CONSTRAINT FK_2ACA45897D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE olz_text ADD CONSTRAINT FK_2ACA45891A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_2ACA45892B18554A ON olz_text (owner_user_id)');
        $this->addSql('CREATE INDEX IDX_2ACA45895A75A473 ON olz_text (owner_role_id)');
        $this->addSql('CREATE INDEX IDX_2ACA45897D182D95 ON olz_text (created_by_user_id)');
        $this->addSql('CREATE INDEX IDX_2ACA45891A04EF5A ON olz_text (last_modified_by_user_id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE olz_text DROP FOREIGN KEY FK_2ACA45892B18554A');
        $this->addSql('ALTER TABLE olz_text DROP FOREIGN KEY FK_2ACA45895A75A473');
        $this->addSql('ALTER TABLE olz_text DROP FOREIGN KEY FK_2ACA45897D182D95');
        $this->addSql('ALTER TABLE olz_text DROP FOREIGN KEY FK_2ACA45891A04EF5A');
        $this->addSql('DROP INDEX IDX_2ACA45892B18554A ON olz_text');
        $this->addSql('DROP INDEX IDX_2ACA45895A75A473 ON olz_text');
        $this->addSql('DROP INDEX IDX_2ACA45897D182D95 ON olz_text');
        $this->addSql('DROP INDEX IDX_2ACA45891A04EF5A ON olz_text');
        $this->addSql('ALTER TABLE olz_text DROP owner_user_id, DROP owner_role_id, DROP created_by_user_id, DROP last_modified_by_user_id, DROP created_at, DROP last_modified_at');
    }
}
