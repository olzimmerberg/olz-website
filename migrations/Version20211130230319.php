<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211130230319 extends AbstractMigration {
    public function getDescription(): string {
        return 'Remove newsletter, add user metadata';
    }

    public function up(Schema $schema): void {
        $this->addSql('DROP TABLE newsletter');
        $this->addSql('DROP TABLE rundmail');

        $this->addSql('ALTER TABLE aktuell ADD newsletter_tmp TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE blog ADD newsletter_tmp TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE forum ADD newsletter_tmp TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE termine ADD newsletter_tmp TINYINT(1) DEFAULT NULL');

        $this->addSql('UPDATE aktuell SET newsletter_tmp=\'0\' WHERE newsletter=\'0\'');
        $this->addSql('UPDATE aktuell SET newsletter_tmp=\'1\' WHERE newsletter IS NULL OR newsletter!=\'0\'');
        $this->addSql('UPDATE blog SET newsletter_tmp=\'0\' WHERE newsletter=\'0\'');
        $this->addSql('UPDATE blog SET newsletter_tmp=\'1\' WHERE newsletter IS NULL OR newsletter!=\'0\'');
        $this->addSql('UPDATE forum SET newsletter_tmp=\'0\' WHERE newsletter=\'0\'');
        $this->addSql('UPDATE forum SET newsletter_tmp=\'1\' WHERE newsletter IS NULL OR newsletter!=\'0\'');
        $this->addSql('UPDATE termine SET newsletter_tmp=\'1\' WHERE newsletter=\'1\'');
        $this->addSql('UPDATE termine SET newsletter_tmp=\'0\' WHERE newsletter IS NULL OR newsletter!=\'1\'');

        $this->addSql('ALTER TABLE aktuell DROP newsletter');
        $this->addSql('ALTER TABLE blog DROP newsletter');
        $this->addSql('ALTER TABLE forum DROP newsletter');
        $this->addSql('ALTER TABLE termine DROP newsletter');

        $this->addSql('ALTER TABLE aktuell ADD newsletter TINYINT(1) DEFAULT \'1\' NOT NULL');
        $this->addSql('ALTER TABLE blog ADD newsletter TINYINT(1) DEFAULT \'1\' NOT NULL');
        $this->addSql('ALTER TABLE forum ADD newsletter TINYINT(1) DEFAULT \'1\' NOT NULL');
        $this->addSql('ALTER TABLE termine ADD newsletter TINYINT(1) DEFAULT \'0\' NOT NULL');

        $this->addSql('UPDATE aktuell SET newsletter=newsletter_tmp WHERE 1');
        $this->addSql('UPDATE blog SET newsletter=newsletter_tmp WHERE 1');
        $this->addSql('UPDATE forum SET newsletter=newsletter_tmp WHERE 1');
        $this->addSql('UPDATE termine SET newsletter=newsletter_tmp WHERE 1');

        $this->addSql('ALTER TABLE aktuell DROP newsletter_tmp');
        $this->addSql('ALTER TABLE blog DROP newsletter_tmp');
        $this->addSql('ALTER TABLE forum DROP newsletter_tmp');
        $this->addSql('ALTER TABLE termine DROP newsletter_tmp');

        $this->addSql('ALTER TABLE aktuell DROP newsletter_datum');
        $this->addSql('ALTER TABLE blog DROP newsletter_datum');
        $this->addSql('ALTER TABLE forum DROP newsletter_datum');
        $this->addSql('ALTER TABLE termine DROP newsletter_datum, DROP newsletter_anmeldung');
        $this->addSql('ALTER TABLE users ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD last_login_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('CREATE TABLE newsletter (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, kategorie LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, reg_date DATE DEFAULT NULL, uid VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, on_off INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE aktuell ADD newsletter_datum DATETIME DEFAULT NULL, CHANGE newsletter newsletter INT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE blog ADD newsletter_datum DATETIME DEFAULT NULL, CHANGE newsletter newsletter INT DEFAULT NULL');
        $this->addSql('ALTER TABLE forum ADD newsletter_datum DATETIME DEFAULT NULL, CHANGE newsletter newsletter INT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE termine ADD newsletter_datum DATETIME DEFAULT NULL, ADD newsletter_anmeldung DATETIME DEFAULT NULL, CHANGE newsletter newsletter INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users DROP created_at, DROP last_modified_at, DROP last_login_at');
    }
}
