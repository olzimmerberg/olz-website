<?php

namespace Olz\Entity\News;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Role;
use Olz\Entity\User;
use Olz\Repository\News\NewsRepository;

#[ORM\Table(name: 'aktuell')]
#[ORM\Index(name: 'datum_index', columns: ['datum'])]
#[ORM\Index(name: 'published_index', columns: ['published_date', 'published_time'])]
#[ORM\Entity(repositoryClass: NewsRepository::class)]
class NewsEntry extends OlzEntity {
    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $termin;

    // @deprecated Use `published_date`
    #[ORM\Column(type: 'date', nullable: false)]
    private $datum;

    #[ORM\Column(type: 'date', nullable: false)]
    private $published_date;

    #[ORM\Column(type: 'time', nullable: true)]
    private $published_time;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 1])]
    private $newsletter;
    // @deprecated Use `title`
    #[ORM\Column(type: 'text', nullable: true)]
    private $titel;
    // @deprecated Use `teaser`
    #[ORM\Column(type: 'text', nullable: true)]
    private $text;
    // @deprecated Use `content`
    #[ORM\Column(type: 'text', nullable: true)]
    private $textlang;

    #[ORM\Column(type: 'text', nullable: false)]
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private $teaser;

    #[ORM\Column(type: 'text', nullable: true)]
    private $content;

    #[ORM\Column(type: 'text', nullable: true)]
    private $image_ids;
    // @deprecated Use `external_url`
    #[ORM\Column(type: 'text', nullable: true)]
    private $link;
    // @deprecated Use `author_name`
    #[ORM\Column(type: 'string', nullable: true)]
    private $autor;
    // @deprecated Use `author_email`
    #[ORM\Column(type: 'string', nullable: true)]
    private $autor_email;

    #[ORM\Column(type: 'text', nullable: true)]
    private $external_url;

    #[ORM\Column(type: 'string', nullable: true)]
    private $author_name;

    #[ORM\Column(type: 'string', nullable: true)]
    private $author_email;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'author_user_id', referencedColumnName: 'id', nullable: true)]
    private $author_user;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(name: 'author_role_id', referencedColumnName: 'id', nullable: true)]
    private $author_role;
    // @deprecated Use format
    #[ORM\Column(type: 'text', nullable: false)]
    private $typ;

    #[ORM\Column(type: 'text', nullable: false)]
    private $format;

    #[ORM\Column(type: 'text', nullable: false, options: ['default' => ''])]
    private $tags;

    // @deprecated No replacement
    #[ORM\Column(type: 'text', nullable: true)]
    private $bild1;

    // @deprecated No replacement
    #[ORM\Column(type: 'integer', nullable: true)]
    private $bild1_breite;

    // @deprecated No replacement
    #[ORM\Column(type: 'text', nullable: true)]
    private $bild1_text;

    // @deprecated No replacement
    #[ORM\Column(type: 'text', nullable: true)]
    private $bild2;

    // @deprecated No replacement
    #[ORM\Column(type: 'integer', nullable: true)]
    private $bild2_breite;

    // @deprecated No replacement
    #[ORM\Column(type: 'text', nullable: true)]
    private $bild3;

    // @deprecated No replacement
    #[ORM\Column(type: 'integer', nullable: true)]
    private $bild3_breite;

    // @deprecated Use `published_time`
    #[ORM\Column(type: 'time', nullable: true)]
    private $zeit;

    #[ORM\Column(type: 'integer', nullable: false, options: ['default' => 0])]
    private $counter;
    // PRIMARY KEY (`id`),
    // KEY `datum` (`datum`)

    public function getId() {
        return $this->id;
    }

    public function setId($new_value) {
        $this->id = $new_value;
    }

    public function getPublishedDate() {
        return $this->datum;
    }

    public function setPublishedDate($new_value) {
        $this->datum = $new_value;
        $this->published_date = $new_value;
    }

    public function getPublishedTime() {
        return $this->zeit;
    }

    public function setPublishedTime($new_value) {
        $this->zeit = $new_value;
        $this->published_time = $new_value;
    }

    public function getFormat() {
        return $this->typ;
    }

    public function setFormat($new_value) {
        $this->typ = $new_value;
        $this->format = $new_value;
    }

    public function getAuthorName() {
        return $this->autor;
    }

    public function setAuthorName($new_value) {
        $this->autor = $new_value;
        $this->author_name = $new_value;
    }

    public function getAuthorEmail() {
        return $this->autor_email;
    }

    public function setAuthorEmail($new_value) {
        $this->autor_email = $new_value;
        $this->author_email = $new_value;
    }

    public function getAuthorUser() {
        return $this->author_user;
    }

    public function setAuthorUser($new_value) {
        $this->author_user = $new_value;
    }

    public function getAuthorRole() {
        return $this->author_role;
    }

    public function setAuthorRole($new_value) {
        $this->author_role = $new_value;
    }

    public function getTitle() {
        return $this->titel;
    }

    public function setTitle($new_value) {
        $this->titel = $new_value;
        $this->title = $new_value;
    }

    public function getTeaser() {
        return $this->text;
    }

    public function setTeaser($new_value) {
        $this->text = $new_value;
    }

    public function getContent() {
        return $this->textlang;
    }

    public function setContent($new_value) {
        $this->textlang = $new_value;
    }

    public function getImageIds() {
        if ($this->image_ids == null) {
            return null;
        }
        return json_decode($this->image_ids, true);
    }

    public function setImageIds($new_value) {
        $this->image_ids = json_encode($new_value);
    }

    public function getTags() {
        return $this->tags;
    }

    public function setTags($new_value) {
        $this->tags = $new_value;
    }

    public function getExternalUrl() {
        return $this->link;
    }

    public function setExternalUrl($new_value) {
        $this->link = $new_value;
    }

    public function getTermin() {
        return $this->termin;
    }

    public function setTermin($new_value) {
        $this->termin = $new_value;
    }

    public function getCounter() {
        return $this->counter;
    }

    public function setCounter($new_value) {
        $this->counter = $new_value;
    }

    /** @deprecated */
    public function getNewsletter() {
        return $this->newsletter;
    }

    /** @deprecated */
    public function setNewsletter($new_value) {
        $this->newsletter = $new_value;
    }
}
