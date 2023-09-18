<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230918192344 extends AbstractMigration {
    public function getDescription(): string {
        return 'Add TerminLabel, TerminTemplate, TerminNotification, and TerminNotificationTemplate';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE termin_labels (id INT AUTO_INCREMENT NOT NULL, owner_user_id INT DEFAULT NULL, owner_role_id INT DEFAULT NULL, created_by_user_id INT DEFAULT NULL, last_modified_by_user_id INT DEFAULT NULL, name VARCHAR(127) NOT NULL, details LONGTEXT DEFAULT NULL, icon LONGTEXT DEFAULT NULL, on_off INT DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_A3B090E02B18554A (owner_user_id), INDEX IDX_A3B090E05A75A473 (owner_role_id), INDEX IDX_A3B090E07D182D95 (created_by_user_id), INDEX IDX_A3B090E01A04EF5A (last_modified_by_user_id), INDEX name_index (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE termin_notification_templates (id BIGINT AUTO_INCREMENT NOT NULL, termin_template_id INT NOT NULL, recipient_user_id INT DEFAULT NULL, recipient_role_id INT DEFAULT NULL, fires_earlier_seconds INT DEFAULT NULL, title LONGTEXT NOT NULL, content LONGTEXT DEFAULT NULL, recipient_termin_owners TINYINT(1) DEFAULT 0 NOT NULL, recipient_termin_volunteers TINYINT(1) DEFAULT 0 NOT NULL, recipient_termin_participants TINYINT(1) DEFAULT 0 NOT NULL, INDEX IDX_43613C90B15EFB97 (recipient_user_id), INDEX IDX_43613C90C0330AAE (recipient_role_id), INDEX termin_template_index (termin_template_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE termin_notifications (id BIGINT AUTO_INCREMENT NOT NULL, termin_id INT NOT NULL, recipient_user_id INT DEFAULT NULL, recipient_role_id INT DEFAULT NULL, fires_at DATETIME NOT NULL, title LONGTEXT NOT NULL, content LONGTEXT DEFAULT NULL, recipient_termin_owners TINYINT(1) DEFAULT 0 NOT NULL, recipient_termin_volunteers TINYINT(1) DEFAULT 0 NOT NULL, recipient_termin_participants TINYINT(1) DEFAULT 0 NOT NULL, INDEX IDX_23876048B15EFB97 (recipient_user_id), INDEX IDX_23876048C0330AAE (recipient_role_id), INDEX termin_index (termin_id), INDEX fires_at_index (fires_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE termin_templates (id INT AUTO_INCREMENT NOT NULL, location_id INT DEFAULT NULL, owner_user_id INT DEFAULT NULL, owner_role_id INT DEFAULT NULL, created_by_user_id INT DEFAULT NULL, last_modified_by_user_id INT DEFAULT NULL, start_time TIME DEFAULT NULL, duration_seconds INT DEFAULT NULL, deadline_earlier_seconds INT DEFAULT NULL, deadline_time TIME DEFAULT NULL, min_participants INT DEFAULT NULL, max_participants INT DEFAULT NULL, min_volunteers INT DEFAULT NULL, max_volunteers INT DEFAULT NULL, newsletter TINYINT(1) DEFAULT 0 NOT NULL, title LONGTEXT DEFAULT NULL, text LONGTEXT DEFAULT NULL, link LONGTEXT DEFAULT NULL, types VARCHAR(255) DEFAULT NULL, image_ids LONGTEXT DEFAULT NULL, on_off INT DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, last_modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_A2ECDD2964D218E (location_id), INDEX IDX_A2ECDD292B18554A (owner_user_id), INDEX IDX_A2ECDD295A75A473 (owner_role_id), INDEX IDX_A2ECDD297D182D95 (created_by_user_id), INDEX IDX_A2ECDD291A04EF5A (last_modified_by_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE termin_template_label_map (termin_template_id INT NOT NULL, label_id INT NOT NULL, INDEX IDX_D1B03BE2324A4BBA (termin_template_id), INDEX IDX_D1B03BE233B92F39 (label_id), PRIMARY KEY(termin_template_id, label_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE termin_label_map (termin_id INT NOT NULL, label_id INT NOT NULL, INDEX IDX_6A8B53A8CA0B7C00 (termin_id), INDEX IDX_6A8B53A833B92F39 (label_id), PRIMARY KEY(termin_id, label_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE termin_labels ADD CONSTRAINT FK_A3B090E02B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE termin_labels ADD CONSTRAINT FK_A3B090E05A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE termin_labels ADD CONSTRAINT FK_A3B090E07D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE termin_labels ADD CONSTRAINT FK_A3B090E01A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE termin_notification_templates ADD CONSTRAINT FK_43613C90324A4BBA FOREIGN KEY (termin_template_id) REFERENCES termin_templates (id)');
        $this->addSql('ALTER TABLE termin_notification_templates ADD CONSTRAINT FK_43613C90B15EFB97 FOREIGN KEY (recipient_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE termin_notification_templates ADD CONSTRAINT FK_43613C90C0330AAE FOREIGN KEY (recipient_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE termin_notifications ADD CONSTRAINT FK_23876048CA0B7C00 FOREIGN KEY (termin_id) REFERENCES termine (id)');
        $this->addSql('ALTER TABLE termin_notifications ADD CONSTRAINT FK_23876048B15EFB97 FOREIGN KEY (recipient_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE termin_notifications ADD CONSTRAINT FK_23876048C0330AAE FOREIGN KEY (recipient_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE termin_templates ADD CONSTRAINT FK_A2ECDD2964D218E FOREIGN KEY (location_id) REFERENCES termin_locations (id)');
        $this->addSql('ALTER TABLE termin_templates ADD CONSTRAINT FK_A2ECDD292B18554A FOREIGN KEY (owner_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE termin_templates ADD CONSTRAINT FK_A2ECDD295A75A473 FOREIGN KEY (owner_role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE termin_templates ADD CONSTRAINT FK_A2ECDD297D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE termin_templates ADD CONSTRAINT FK_A2ECDD291A04EF5A FOREIGN KEY (last_modified_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE termin_template_label_map ADD CONSTRAINT FK_D1B03BE2324A4BBA FOREIGN KEY (termin_template_id) REFERENCES termin_templates (id)');
        $this->addSql('ALTER TABLE termin_template_label_map ADD CONSTRAINT FK_D1B03BE233B92F39 FOREIGN KEY (label_id) REFERENCES termin_labels (id)');
        $this->addSql('ALTER TABLE termin_label_map ADD CONSTRAINT FK_6A8B53A8CA0B7C00 FOREIGN KEY (termin_id) REFERENCES termine (id)');
        $this->addSql('ALTER TABLE termin_label_map ADD CONSTRAINT FK_6A8B53A833B92F39 FOREIGN KEY (label_id) REFERENCES termin_labels (id)');
        $this->addSql('ALTER TABLE aktuell ADD published_date DATE NOT NULL AFTER id, ADD published_time TIME DEFAULT NULL AFTER published_date, ADD title LONGTEXT NOT NULL AFTER published_time, ADD teaser LONGTEXT DEFAULT NULL AFTER title, ADD content LONGTEXT DEFAULT NULL AFTER teaser, ADD external_url LONGTEXT DEFAULT NULL AFTER link, ADD author_name VARCHAR(255) DEFAULT NULL AFTER content, ADD author_email VARCHAR(255) DEFAULT NULL AFTER author_name, ADD format LONGTEXT NOT NULL AFTER id');
        $this->addSql('CREATE INDEX published_index ON aktuell (published_date, published_time)');
        $this->addSql('ALTER TABLE termine ADD start_date DATE NOT NULL AFTER id, ADD start_time TIME DEFAULT NULL AFTER start_date, ADD end_date DATE DEFAULT NULL AFTER start_time, ADD end_time TIME DEFAULT NULL AFTER end_date, ADD title LONGTEXT DEFAULT NULL AFTER end_time');
        $this->addSql('UPDATE aktuell SET published_date=datum, published_time=zeit, title=titel, teaser=text, content=textlang, external_url=link, author_name=autor, author_email=autor_email, format=typ');
        $this->addSql('UPDATE termine SET start_date=datum, start_time=zeit, end_date=datum_end, end_time=zeit_end, title=titel');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE termin_labels DROP FOREIGN KEY FK_A3B090E02B18554A');
        $this->addSql('ALTER TABLE termin_labels DROP FOREIGN KEY FK_A3B090E05A75A473');
        $this->addSql('ALTER TABLE termin_labels DROP FOREIGN KEY FK_A3B090E07D182D95');
        $this->addSql('ALTER TABLE termin_labels DROP FOREIGN KEY FK_A3B090E01A04EF5A');
        $this->addSql('ALTER TABLE termin_notification_templates DROP FOREIGN KEY FK_43613C90324A4BBA');
        $this->addSql('ALTER TABLE termin_notification_templates DROP FOREIGN KEY FK_43613C90B15EFB97');
        $this->addSql('ALTER TABLE termin_notification_templates DROP FOREIGN KEY FK_43613C90C0330AAE');
        $this->addSql('ALTER TABLE termin_notifications DROP FOREIGN KEY FK_23876048CA0B7C00');
        $this->addSql('ALTER TABLE termin_notifications DROP FOREIGN KEY FK_23876048B15EFB97');
        $this->addSql('ALTER TABLE termin_notifications DROP FOREIGN KEY FK_23876048C0330AAE');
        $this->addSql('ALTER TABLE termin_templates DROP FOREIGN KEY FK_A2ECDD2964D218E');
        $this->addSql('ALTER TABLE termin_templates DROP FOREIGN KEY FK_A2ECDD292B18554A');
        $this->addSql('ALTER TABLE termin_templates DROP FOREIGN KEY FK_A2ECDD295A75A473');
        $this->addSql('ALTER TABLE termin_templates DROP FOREIGN KEY FK_A2ECDD297D182D95');
        $this->addSql('ALTER TABLE termin_templates DROP FOREIGN KEY FK_A2ECDD291A04EF5A');
        $this->addSql('ALTER TABLE termin_template_label_map DROP FOREIGN KEY FK_D1B03BE2324A4BBA');
        $this->addSql('ALTER TABLE termin_template_label_map DROP FOREIGN KEY FK_D1B03BE233B92F39');
        $this->addSql('ALTER TABLE termin_label_map DROP FOREIGN KEY FK_6A8B53A8CA0B7C00');
        $this->addSql('ALTER TABLE termin_label_map DROP FOREIGN KEY FK_6A8B53A833B92F39');
        $this->addSql('DROP TABLE termin_labels');
        $this->addSql('DROP TABLE termin_notification_templates');
        $this->addSql('DROP TABLE termin_notifications');
        $this->addSql('DROP TABLE termin_templates');
        $this->addSql('DROP TABLE termin_template_label_map');
        $this->addSql('DROP TABLE termin_label_map');
        $this->addSql('DROP INDEX published_index ON aktuell');
        $this->addSql('ALTER TABLE aktuell DROP published_date, DROP published_time, DROP title, DROP teaser, DROP content, DROP external_url, DROP author_name, DROP author_email, DROP format');
        $this->addSql('ALTER TABLE termine DROP start_date, DROP start_time, DROP end_date, DROP end_time, DROP title');
    }
}
