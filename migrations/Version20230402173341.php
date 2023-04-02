<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230402173341 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migrate blog -> aktuell';
    }

    public function up(Schema $schema): void {
        $rows = $this->connection->fetchAllAssociative('SELECT * FROM blog');
        foreach ($rows as $row) {
            $datetime = new \DateTime($row['datum'].' '.$row['zeit']);
            $isodatetime = $datetime->format('Y-m-d H:i:s');
            $aktuell = [
                'id' => intval($row['id']) + 6400,
                'titel' => $row['titel'],
                'datum' => $row['datum'],
                'zeit' => $row['zeit'],
                'textlang' => $row['text'],
                'autor' => $row['autor'],
                'autor_email' => null,
                'link' => $row['linkext'],
                'bild1' => $row['bild1'],
                'bild1_breite' => $row['bild1_breite'],
                'bild2' => $row['bild2'],
                'bild2_breite' => $row['bild2_breite'],
                'typ' => 'kaderblog',
                'on_off' => $row['on_off'],
                'counter' => $row['counter'],
                'created_at' => $isodatetime,
                'last_modified_at' => $isodatetime,
                'text' => json_encode([ // backup
                    'file1' => $row['file1'],
                    'file1_name' => $row['file1_name'],
                    'file2' => $row['file2'],
                    'file2_name' => $row['file2_name'],
                ]),
            ];
            $this->addSql("INSERT INTO aktuell (id, titel, datum, zeit, text, textlang, autor, autor_email, link, bild1, bild1_breite, bild2, bild2_breite, typ, on_off, counter, created_at, last_modified_at, tags, termin, newsletter) VALUES (:id, :titel, :datum, :zeit, :text, :textlang, :autor, :autor_email, :link, :bild1, :bild1_breite, :bild2, :bild2_breite, :typ, :on_off, :counter, :created_at, :last_modified_at, '', '0', '0')", $aktuell);
        }
    }

    public function down(Schema $schema): void {
    }
}
