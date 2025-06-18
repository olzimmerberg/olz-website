<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250618220717 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add AHV-Number and dress size';
    }

    public function up(Schema $schema): void {
        $this->addSql(<<<'SQL'
                ALTER TABLE users ADD ahv_number VARCHAR(17) DEFAULT NULL, ADD dress_size VARCHAR(4) DEFAULT NULL COMMENT '3XS, XXS, XS, S, M, L, XL, XXL, 3XL'
            SQL);
    }

    public function down(Schema $schema): void {
        $this->addSql(<<<'SQL'
                ALTER TABLE users DROP ahv_number, DROP dress_size
            SQL);
    }
}
