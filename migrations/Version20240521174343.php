<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240521174343 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add FAQ tables';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE question_categories (id INT AUTO_INCREMENT NOT NULL, owner_user_id INT DEFAULT NULL, owner_role_id INT DEFAULT NULL, created_by_user_id INT DEFAULT NULL, last_modified_by_user_id INT DEFAULT NULL, on_off INT DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, position INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_5D27D9E02B18554A (owner_user_id), INDEX IDX_5D27D9E05A75A473 (owner_role_id), INDEX IDX_5D27D9E07D182D95 (created_by_user_id), INDEX IDX_5D27D9E01A04EF5A (last_modified_by_user_id), INDEX position_index (on_off, position), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE questions (id INT AUTO_INCREMENT NOT NULL, owner_user_id INT DEFAULT NULL, owner_role_id INT DEFAULT NULL, created_by_user_id INT DEFAULT NULL, last_modified_by_user_id INT DEFAULT NULL, category_id INT DEFAULT NULL, on_off INT DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ident VARCHAR(31) NOT NULL, position_within_category INT NOT NULL, question LONGTEXT NOT NULL, answer LONGTEXT DEFAULT NULL, INDEX IDX_8ADC54D52B18554A (owner_user_id), INDEX IDX_8ADC54D55A75A473 (owner_role_id), INDEX IDX_8ADC54D57D182D95 (created_by_user_id), INDEX IDX_8ADC54D51A04EF5A (last_modified_by_user_id), INDEX IDX_8ADC54D512469DE2 (category_id), INDEX ident_index (on_off, ident), INDEX category_position_index (on_off, category_id, position_within_category), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE question_categories ADD CONSTRAINT FK_5D27D9E02B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE question_categories ADD CONSTRAINT FK_5D27D9E05A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE question_categories ADD CONSTRAINT FK_5D27D9E07D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE question_categories ADD CONSTRAINT FK_5D27D9E01A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D52B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D55A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D57D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D51A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D512469DE2 FOREIGN KEY (category_id) REFERENCES question_categories (id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE question_categories DROP FOREIGN KEY FK_5D27D9E02B18554A');
        $this->addSql('ALTER TABLE question_categories DROP FOREIGN KEY FK_5D27D9E05A75A473');
        $this->addSql('ALTER TABLE question_categories DROP FOREIGN KEY FK_5D27D9E07D182D95');
        $this->addSql('ALTER TABLE question_categories DROP FOREIGN KEY FK_5D27D9E01A04EF5A');
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D52B18554A');
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D55A75A473');
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D57D182D95');
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D51A04EF5A');
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D512469DE2');
        $this->addSql('DROP TABLE question_categories');
        $this->addSql('DROP TABLE questions');
    }
}
