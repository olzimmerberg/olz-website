<?php

namespace Olz\Entity\News;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\Common\DataStorageInterface;
use Olz\Entity\Common\DataStorageTrait;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Roles\Role;
use Olz\Entity\User;
use Olz\Repository\News\NewsRepository;

#[ORM\Table(name: 'news')]
#[ORM\Index(name: 'published_index', columns: ['published_date', 'published_time'])]
#[ORM\Entity(repositoryClass: NewsRepository::class)]
class NewsEntry extends OlzEntity implements DataStorageInterface {
    use DataStorageTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $termin;

    #[ORM\Column(type: 'date', nullable: false)]
    private $published_date;

    #[ORM\Column(type: 'time', nullable: true)]
    private $published_time;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 1])]
    private $newsletter;

    #[ORM\Column(type: 'text', nullable: false)]
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private $teaser;

    #[ORM\Column(type: 'text', nullable: true)]
    private $content;

    #[ORM\Column(type: 'text', nullable: true)]
    private $image_ids;

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

    #[ORM\Column(type: 'text', nullable: false)]
    private $format;

    #[ORM\Column(type: 'text', nullable: false, options: ['default' => ''])]
    private $tags;

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
        return $this->published_date;
    }

    public function setPublishedDate($new_value) {
        $this->published_date = $new_value;
    }

    public function getPublishedTime() {
        return $this->published_time;
    }

    public function setPublishedTime($new_value) {
        $this->published_time = $new_value;
    }

    public function getFormat() {
        return $this->format;
    }

    public function setFormat($new_value) {
        $this->format = $new_value;
    }

    public function getAuthorName() {
        return $this->author_name;
    }

    public function setAuthorName($new_value) {
        $this->author_name = $new_value;
    }

    public function getAuthorEmail() {
        return $this->author_email;
    }

    public function setAuthorEmail($new_value) {
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
        return $this->title;
    }

    public function setTitle($new_value) {
        $this->title = $new_value;
    }

    public function getTeaser() {
        return $this->teaser;
    }

    public function setTeaser($new_value) {
        $this->teaser = $new_value;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($new_value) {
        $this->content = $new_value;
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
        return $this->external_url;
    }

    public function setExternalUrl($new_value) {
        $this->external_url = $new_value;
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

    public static function getEntityNameForStorage(): string {
        return 'news';
    }

    public function getEntityIdForStorage(): string {
        return "{$this->getId()}";
    }
}
