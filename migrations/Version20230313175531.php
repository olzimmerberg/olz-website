<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230313175531 extends AbstractMigration {
    public function getDescription(): string {
        return 'Panini2024';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE panini24 (id BIGINT AUTO_INCREMENT NOT NULL, owner_user_id INT DEFAULT NULL, owner_role_id INT DEFAULT NULL, created_by_user_id INT DEFAULT NULL, last_modified_by_user_id INT DEFAULT NULL, line1 VARCHAR(255) NOT NULL, line2 VARCHAR(255) DEFAULT NULL, association VARCHAR(255) DEFAULT NULL, img_src VARCHAR(255) NOT NULL, img_style VARCHAR(255) NOT NULL, is_landscape TINYINT(1) NOT NULL, has_top TINYINT(1) NOT NULL, on_off INT DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_1254A2E52B18554A (owner_user_id), INDEX IDX_1254A2E55A75A473 (owner_role_id), INDEX IDX_1254A2E57D182D95 (created_by_user_id), INDEX IDX_1254A2E51A04EF5A (last_modified_by_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE panini24 ADD CONSTRAINT FK_1254A2E52B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE panini24 ADD CONSTRAINT FK_1254A2E55A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE panini24 ADD CONSTRAINT FK_1254A2E57D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE panini24 ADD CONSTRAINT FK_1254A2E51A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE panini24 DROP FOREIGN KEY FK_1254A2E52B18554A');
        $this->addSql('ALTER TABLE panini24 DROP FOREIGN KEY FK_1254A2E55A75A473');
        $this->addSql('ALTER TABLE panini24 DROP FOREIGN KEY FK_1254A2E57D182D95');
        $this->addSql('ALTER TABLE panini24 DROP FOREIGN KEY FK_1254A2E51A04EF5A');
        $this->addSql('DROP TABLE panini24');
    }
}
