<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250526204822 extends AbstractMigration {
    public function getDescription(): string {
        return 'Streamline position columns';
    }

    public function up(Schema $schema): void {
        // downloads
        $this->addSql(<<<'SQL'
                ALTER TABLE downloads CHANGE position position FLOAT NOT NULL
            SQL);

        // links
        $this->addSql(<<<'SQL'
                ALTER TABLE links CHANGE position position FLOAT NOT NULL
            SQL);

        // question_categories
        $this->addSql(<<<'SQL'
                ALTER TABLE question_categories CHANGE position position FLOAT NOT NULL
            SQL);

        // questions
        $this->addSql(<<<'SQL'
                ALTER TABLE questions CHANGE position_within_category position_within_category FLOAT NOT NULL
            SQL);

        // roles
        $this->addSql(<<<'SQL'
                ALTER TABLE roles
                ADD position_within_parent FLOAT DEFAULT NULL COMMENT 'null: hide role',
                ADD featured_position FLOAT DEFAULT NULL COMMENT 'null: not featured'
            SQL);
        $this->addSql(<<<'SQL'
                UPDATE roles SET
                position_within_parent=(CASE WHEN index_within_parent < 0 THEN NULL ELSE index_within_parent END),
                featured_position=featured_index
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE roles
                DROP index_within_parent,
                DROP featured_index
            SQL);

        // termin_labels
        $this->addSql(<<<'SQL'
                ALTER TABLE termin_labels CHANGE position position FLOAT NOT NULL
            SQL);
    }

    public function down(Schema $schema): void {
        $this->addSql(<<<'SQL'
                ALTER TABLE downloads CHANGE position position INT NOT NULL
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE links CHANGE position position INT NOT NULL
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE questions CHANGE position_within_category position_within_category INT NOT NULL
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE question_categories CHANGE position position INT NOT NULL
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE roles
                ADD index_within_parent INT DEFAULT NULL COMMENT 'negative value: hide role',
                ADD featured_index INT DEFAULT NULL,
                DROP position_within_parent,
                DROP featured_position
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE termin_labels CHANGE position position INT NOT NULL
            SQL);
    }
}
