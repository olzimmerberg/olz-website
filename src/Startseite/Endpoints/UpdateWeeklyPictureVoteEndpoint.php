<?php

namespace Olz\Startseite\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\Startseite\WeeklyPicture;
use Olz\Entity\Startseite\WeeklyPictureVote;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class UpdateWeeklyPictureVoteEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'UpdateWeeklyPictureVoteEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
            'id' => new FieldTypes\IntegerField([]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'weeklyPictureId' => new FieldTypes\IntegerField(['min_value' => 1]),
            'vote' => new FieldTypes\IntegerField(['min_value' => -1, 'max_value' => 1]),
        ]]);
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $user = $this->authUtils()->getCurrentUser();
        $weekly_picture_id = $input['weeklyPictureId'];
        $weekly_picture_repo = $this->entityManager()->getRepository(WeeklyPicture::class);
        $weekly_picture = $weekly_picture_repo->findOneBy(['id' => $weekly_picture_id]);
        if (!$weekly_picture) {
            throw new HttpError(404, "Kein solches Bild der Woche!");
        }
        $weekly_picture_vote_repo = $this->entityManager()->getRepository(WeeklyPictureVote::class);
        $weekly_picture_vote = $weekly_picture_vote_repo->findOneBy([
            'created_by_user' => $user,
            'weekly_picture' => $weekly_picture,
        ]);
        if (!$weekly_picture_vote) {
            $weekly_picture_vote = new WeeklyPictureVote();
        }

        $now = new \DateTime($this->dateUtils()->getIsoNow());

        $weekly_picture_vote->setCreatedAt($now);
        $weekly_picture_vote->setCreatedByUser($user);
        $weekly_picture_vote->setWeeklyPicture($weekly_picture);
        $weekly_picture_vote->setVote($input['vote']);

        $this->entityManager()->persist($weekly_picture_vote);
        $this->entityManager()->flush();

        return [
            'status' => 'OK',
            'id' => $weekly_picture_vote->getId(),
        ];
    }
}
