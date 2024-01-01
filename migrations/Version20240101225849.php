<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240101225849 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add birthdate and num_mispunches to panini';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE panini24 ADD birthdate DATE DEFAULT NULL, ADD num_mispunches INT DEFAULT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE panini24 DROP birthdate, DROP num_mispunches');
    }
}
