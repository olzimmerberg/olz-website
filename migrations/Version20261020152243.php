<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261020152243 extends AbstractMigration {
    public function getDescription(): string {
        return 'Termin-News, Parent Termin';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE news ADD termin_id INT DEFAULT NULL, DROP termin');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD39950CA0B7C00 FOREIGN KEY (termin_id) REFERENCES termine (id)');
        $this->addSql('CREATE INDEX IDX_1DD39950CA0B7C00 ON news (termin_id)');
        $this->addSql('ALTER TABLE termine ADD parent_termin_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE termine ADD CONSTRAINT FK_168C0A8F3B4053EE FOREIGN KEY (parent_termin_id) REFERENCES termine (id)');
        $this->addSql('CREATE INDEX IDX_168C0A8F3B4053EE ON termine (parent_termin_id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE news DROP FOREIGN KEY FK_1DD39950CA0B7C00');
        $this->addSql('DROP INDEX IDX_1DD39950CA0B7C00 ON news');
        $this->addSql('ALTER TABLE news ADD termin INT NOT NULL, DROP termin_id');
        $this->addSql('ALTER TABLE termine DROP FOREIGN KEY FK_168C0A8F3B4053EE');
        $this->addSql('DROP INDEX IDX_168C0A8F3B4053EE ON termine');
        $this->addSql('ALTER TABLE termine DROP parent_termin_id');
    }
}
