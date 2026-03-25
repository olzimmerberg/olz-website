<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260326192143 extends AbstractMigration {
    public function getDescription(): string {
        return 'News/Termin reaction';
    }

    public function up(Schema $schema): void {
        $this->addSql('CREATE TABLE news_reactions (id BIGINT AUTO_INCREMENT NOT NULL, emoji VARCHAR(15) NOT NULL, news_entry_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_24562072F308EA13 (news_entry_id), INDEX IDX_24562072A76ED395 (user_id), INDEX news_emoji_user_index (news_entry_id, emoji, user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE termin_reactions (id BIGINT AUTO_INCREMENT NOT NULL, emoji VARCHAR(15) NOT NULL, termin_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F5B7DF14CA0B7C00 (termin_id), INDEX IDX_F5B7DF14A76ED395 (user_id), INDEX termin_emoji_user_index (termin_id, emoji, user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE news_reactions ADD CONSTRAINT FK_24562072F308EA13 FOREIGN KEY (news_entry_id) REFERENCES news (id)');
        $this->addSql('ALTER TABLE news_reactions ADD CONSTRAINT FK_24562072A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE termin_reactions ADD CONSTRAINT FK_F5B7DF14CA0B7C00 FOREIGN KEY (termin_id) REFERENCES termine (id)');
        $this->addSql('ALTER TABLE termin_reactions ADD CONSTRAINT FK_F5B7DF14A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE news_reactions DROP FOREIGN KEY FK_24562072F308EA13');
        $this->addSql('ALTER TABLE news_reactions DROP FOREIGN KEY FK_24562072A76ED395');
        $this->addSql('ALTER TABLE termin_reactions DROP FOREIGN KEY FK_F5B7DF14CA0B7C00');
        $this->addSql('ALTER TABLE termin_reactions DROP FOREIGN KEY FK_F5B7DF14A76ED395');
        $this->addSql('DROP TABLE news_reactions');
        $this->addSql('DROP TABLE termin_reactions');
    }
}
