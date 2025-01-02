<?php

namespace Olz\Utils;

use Olz\Entity\Anmelden\Booking;
use Olz\Entity\Anmelden\Registration;
use Olz\Entity\Anmelden\RegistrationInfo;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Faq\Question;
use Olz\Entity\Faq\QuestionCategory;
use Olz\Entity\Karten\Karte;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\Panini2024\Panini2024Picture;
use Olz\Entity\Quiz\Skill;
use Olz\Entity\Quiz\SkillCategory;
use Olz\Entity\Quiz\SkillLevel;
use Olz\Entity\Roles\Role;
use Olz\Entity\Service\Download;
use Olz\Entity\Service\Link;
use Olz\Entity\Snippets\Snippet;
use Olz\Entity\Startseite\WeeklyPicture;
use Olz\Entity\Termine\Termin;
use Olz\Entity\Termine\TerminLabel;
use Olz\Entity\Termine\TerminLocation;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Entity\Users\User;

class EntityUtils {
    use WithUtilsTrait;

    /** @var array<class-string<OlzEntity>> */
    protected static array $olzEntityClasses = [
        Booking::class,
        Registration::class,
        RegistrationInfo::class,
        Question::class,
        QuestionCategory::class,
        Karte::class,
        NewsEntry::class,
        Panini2024Picture::class,
        Skill::class,
        SkillCategory::class,
        SkillLevel::class,
        Role::class,
        Download::class,
        Link::class,
        Snippet::class,
        WeeklyPicture::class,
        Termin::class,
        TerminLabel::class,
        TerminLocation::class,
        TerminTemplate::class,
        User::class,
    ];

    /** @param array{onOff?: bool, ownerUserId?: int, ownerRoleId?: int} $input */
    public function createOlzEntity(OlzEntity $entity, array $input): void {
        $user_repo = $this->entityManager()->getRepository(User::class);
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $current_user = $this->authUtils()->getCurrentUser();
        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());

        $on_off = ($input['onOff'] ?? false) ? 1 : 0;

        $owner_user_id = $input['ownerUserId'] ?? null;
        $owner_user = $current_user;
        if ($owner_user_id) {
            $owner_user = $user_repo->findOneBy(['id' => $owner_user_id]);
        }

        $owner_role_id = $input['ownerRoleId'] ?? null;
        $owner_role = null;
        if ($owner_role_id) {
            $owner_role = $role_repo->findOneBy(['id' => $owner_role_id]);
        }

        $entity->setOnOff($on_off);
        $entity->setOwnerUser($owner_user);
        $entity->setOwnerRole($owner_role);
        $entity->setCreatedAt($now_datetime);
        $entity->setCreatedByUser($current_user);
        $entity->setLastModifiedAt($now_datetime);
        $entity->setLastModifiedByUser($current_user);
    }

    /** @param array{onOff?: bool, ownerUserId?: int, ownerRoleId?: int} $input */
    public function updateOlzEntity(OlzEntity $entity, array $input): void {
        $user_repo = $this->entityManager()->getRepository(User::class);
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $current_user = $this->authUtils()->getCurrentUser();
        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());

        $on_off = ($input['onOff'] ?? null) ? 1 : 0;

        $owner_user_id = $input['ownerUserId'] ?? null;
        if ($owner_user_id) {
            $owner_user = $user_repo->findOneBy(['id' => $owner_user_id]);
            $entity->setOwnerUser($owner_user);
        }

        $owner_role_id = $input['ownerRoleId'] ?? null;
        if ($owner_role_id) {
            $owner_role = $role_repo->findOneBy(['id' => $owner_role_id]);
            $entity->setOwnerRole($owner_role);
        }

        $entity->setOnOff($on_off);
        $entity->setLastModifiedAt($now_datetime);
        $entity->setLastModifiedByUser($current_user);
    }

    /** @param ?array{onOff?: bool, ownerUserId?: int, ownerRoleId?: int} $meta_arg */
    public function canUpdateOlzEntity(
        ?OlzEntity $entity,
        ?array $meta_arg,
        string $edit_permission = 'all',
    ): bool {
        $meta = $meta_arg ?? [];
        $auth_utils = $this->authUtils();
        $current_user = $auth_utils->getCurrentUser();

        if ($auth_utils->hasPermission($edit_permission)) {
            return true;
        }

        $owner_user = $entity?->getOwnerUser();
        if ($owner_user && $current_user?->getId() === $owner_user->getId()) {
            return true;
        }

        $created_by_user = $entity?->getCreatedByUser();
        if ($created_by_user && $current_user?->getId() === $created_by_user->getId()) {
            return true;
        }

        // TODO: Check roles

        return false;
    }

    /** @return array<class-string<OlzEntity>> */
    public function olzEntityClasses(): array {
        return $this::$olzEntityClasses;
    }

    public static function fromEnv(): self {
        return new self();
    }
}
