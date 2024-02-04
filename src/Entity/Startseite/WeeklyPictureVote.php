<?php

namespace Olz\Entity\Startseite;

use Doctrine\ORM\Mapping as ORM;
use Olz\Entity\User;
use Olz\Repository\Startseite\WeeklyPictureVoteRepository;

#[ORM\Table(name: 'weekly_picture_votes')]
#[ORM\Index(name: 'weekly_picture_created_at_index', columns: ['weekly_picture_id', 'created_at'])]
#[ORM\Index(name: 'weekly_picture_created_by_index', columns: ['weekly_picture_id', 'created_by_user_id'])]
#[ORM\Entity(repositoryClass: WeeklyPictureVoteRepository::class)]
class WeeklyPictureVote {
    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    protected $created_at;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by_user_id', referencedColumnName: 'id', nullable: false)]
    protected $created_by_user;

    #[ORM\ManyToOne(targetEntity: WeeklyPicture::class)]
    #[ORM\JoinColumn(name: 'weekly_picture_id', referencedColumnName: 'id', nullable: false)]
    protected $weekly_picture;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $vote;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', nullable: false)]
    #[ORM\GeneratedValue]
    private $id;

    public function getId() {
        return $this->id;
    }

    public function setId($new_id) {
        $this->id = $new_id;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($new_created_at) {
        $this->created_at = $new_created_at;
    }

    public function getCreatedByUser() {
        return $this->created_by_user;
    }

    public function setCreatedByUser($new_created_by_user) {
        $this->created_by_user = $new_created_by_user;
    }

    public function getWeeklyPicture() {
        return $this->weekly_picture;
    }

    public function setWeeklyPicture($new_weekly_picture) {
        $this->weekly_picture = $new_weekly_picture;
    }

    public function getVote() {
        return $this->vote;
    }

    public function setVote($new_vote) {
        $this->vote = $new_vote;
    }
}
