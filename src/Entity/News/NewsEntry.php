<?php

namespace Olz\Entity\News;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\OlzEntity;
use Olz\Repository\News\NewsRepository;

/**
 * @ORM\Entity(repositoryClass=NewsRepository::class)
 * @ORM\Table(
 *     name="aktuell",
 *     indexes={@ORM\Index(name="datum_index", columns={"datum"})},
 * )
 */
class NewsEntry extends OlzEntity {
    /**
     * @ORM\Id @ORM\Column(type="integer", nullable=false) @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $termin;
    /**
     * @ORM\Column(type="date", nullable=false)
     */
    private $datum;
    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": 1})
     */
    private $newsletter;
    // TODO: Rename to `title`
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $titel;
    // TODO: Rename to `teaser`
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;
    // TODO: Rename to `content`
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $textlang;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $image_ids;
    // TODO: Rename to `external_url`
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $link;
    // TODO: Rename to `author`
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $autor;
    /**
     * @ORM\ManyToOne(targetEntity="\Olz\Entity\User")
     * @ORM\JoinColumn(name="author_user_id", referencedColumnName="id", nullable=true)
     */
    private $author_user;
    /**
     * @ORM\ManyToOne(targetEntity="\Olz\Entity\Role")
     * @ORM\JoinColumn(name="author_role_id", referencedColumnName="id", nullable=true)
     */
    private $author_role;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $typ;
    /**
     * @ORM\Column(type="text", nullable=false, options={"default": ""})
     */
    private $tags;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bild1;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bild1_breite;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bild1_text;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bild2;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bild2_breite;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bild3;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bild3_breite;
    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $zeit;
    /**
     * @ORM\Column(type="integer", nullable=false, options={"default": 0})
     */
    private $counter;
    // PRIMARY KEY (`id`),
    // KEY `datum` (`datum`)

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getDate() {
        return $this->datum;
    }

    public function setDate($new_datum) {
        $this->datum = $new_datum;
    }

    public function getTime() {
        return $this->zeit;
    }

    public function setTime($new_zeit) {
        $this->zeit = $new_zeit;
    }

    public function getFormat() {
        return $this->typ;
    }

    public function setFormat($new_format) {
        $this->typ = $new_format;
    }

    public function getAuthor() {
        return $this->autor;
    }

    public function setAuthor($new_autor) {
        $this->autor = $new_autor;
    }

    public function getAuthorUser() {
        return $this->author_user;
    }

    public function setAuthorUser($new_author_user) {
        $this->author_user = $new_author_user;
    }

    public function getAuthorRole() {
        return $this->author_role;
    }

    public function setAuthorRole($new_author_role) {
        $this->author_role = $new_author_role;
    }

    public function getTitle() {
        return $this->titel;
    }

    public function setTitle($new_titel) {
        $this->titel = $new_titel;
    }

    public function getTeaser() {
        return $this->text;
    }

    public function setTeaser($new_text) {
        $this->text = $new_text;
    }

    public function getContent() {
        return $this->textlang;
    }

    public function setContent($new_textlang) {
        $this->textlang = $new_textlang;
    }

    public function getImageIds() {
        if ($this->image_ids == null) {
            return null;
        }
        return json_decode($this->image_ids, true);
    }

    public function setImageIds($new_image_ids) {
        $this->image_ids = json_encode($new_image_ids);
    }

    public function getTags() {
        return $this->tags;
    }

    public function setTags($new_tags) {
        $this->tags = $new_tags;
    }

    public function getExternalUrl() {
        return $this->link;
    }

    public function setExternalUrl($new_external_url) {
        $this->link = $new_external_url;
    }

    public function getTermin() {
        return $this->termin;
    }

    public function setTermin($new_termin) {
        $this->termin = $new_termin;
    }

    public function getCounter() {
        return $this->counter;
    }

    public function setCounter($new_counter) {
        $this->counter = $new_counter;
    }

    /** @deprecated */
    public function getNewsletter() {
        return $this->newsletter;
    }

    /** @deprecated */
    public function setNewsletter($new_newsletter) {
        $this->newsletter = $new_newsletter;
    }
}
