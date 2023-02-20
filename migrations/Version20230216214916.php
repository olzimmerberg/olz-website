<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230216214916 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migrate forum -> aktuell';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE aktuell ADD autor_email VARCHAR(255) DEFAULT NULL');
        $rows = $this->connection->fetchAllAssociative('SELECT * FROM forum');
        foreach ($rows as $row) {
            $name = $row['name'];
            $name2 = $row['name2'];
            $datetime = new \DateTime($row['datum'].' '.$row['zeit']);
            $isodatetime = $datetime->format('Y-m-d H:i:s');
            $aktuell = [
                'id' => intval($row['id']) + 2900,
                'titel' => $name2 ? $name : "Forumseintrag",
                'datum' => $row['datum'],
                'zeit' => $row['zeit'],
                'textlang' => $row['eintrag'],
                'autor' => $name2 ? $name2 : $name,
                'autor_email' => $row['email'],
                'typ' => 'forum',
                'on_off' => $row['on_off'],
                'counter' => 0,
                'created_at' => $isodatetime,
                'last_modified_at' => $isodatetime,
            ];
            $this->addSql("INSERT INTO aktuell (id, titel, datum, zeit, textlang, autor, autor_email, typ, on_off, counter, created_at, last_modified_at, tags, termin, newsletter, text) VALUES (:id, :titel, :datum, :zeit, :textlang, :autor, :autor_email, :typ, :on_off, :counter, :created_at, :last_modified_at, '', '0', '0', '')", $aktuell);
        }
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE aktuell DROP autor_email');
    }
}
