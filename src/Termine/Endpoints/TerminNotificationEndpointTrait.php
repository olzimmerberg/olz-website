<?php

namespace Olz\Termine\Endpoints;

use Olz\Entity\Roles\Role;
use Olz\Entity\Termine\Termin;
use Olz\Entity\Termine\TerminNotification;
use Olz\Entity\Users\User;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzTerminNotificationId int
 * @phpstan-type OlzTerminNotificationData array{
 *   terminId: int,
 *   firesEarlierSeconds: int,
 *   title: non-empty-string,
 *   content: string,
 *   recipientUserId?: ?int,
 *   recipientRoleId?: ?int,
 *   recipientTerminOwnerUser?: ?bool,
 *   recipientTerminOwnerRole?: ?bool,
 *   recipientTerminOrganizer?: ?bool,
 *   recipientTerminVolunteers?: ?bool,
 *   recipientTerminParticipants?: ?bool,
 * }
 */
trait TerminNotificationEndpointTrait {
    use WithUtilsTrait;

    /** @return OlzTerminNotificationData */
    public function getEntityData(TerminNotification $entity): array {
        return [
            'terminId' => $entity->getTermin()->getId() ?? 0,
            'firesEarlierSeconds' => $entity->getFiresEarlierSeconds(),
            'title' => $entity->getTitle() ?: '-',
            'content' => $entity->getContent() ?? '',
            'recipientUserId' => $entity->getRecipientUser()?->getId(),
            'recipientRoleId' => $entity->getRecipientRole()?->getId(),
            'recipientTerminOwnerUser' => $entity->getRecipientTerminOwnerUser(),
            'recipientTerminOwnerRole' => $entity->getRecipientTerminOwnerRole(),
            'recipientTerminOrganizer' => $entity->getRecipientTerminOrganizer(),
            'recipientTerminVolunteers' => $entity->getRecipientTerminVolunteers(),
            'recipientTerminParticipants' => $entity->getRecipientTerminParticipants(),
        ];
    }

    /** @param OlzTerminNotificationData $input_data */
    public function updateEntityWithData(TerminNotification $entity, array $input_data): void {
        $termin_repo = $this->entityManager()->getRepository(Termin::class);
        $termin_id = $input_data['terminId'];
        $termin = $termin_repo->findOneBy(['id' => $termin_id]);
        $this->generalUtils()->checkNotNull($termin, 'TerminNotification->termin must not be null');
        $user_repo = $this->entityManager()->getRepository(User::class);
        $recipient_user_id = $input_data['recipientUserId'] ?? null;
        $recipient_user = $user_repo->findOneBy(['id' => $recipient_user_id]);
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $recipient_role_id = $input_data['recipientRoleId'] ?? null;
        $recipient_role = $role_repo->findOneBy(['id' => $recipient_role_id]);

        $entity->setTermin($termin);
        $entity->setFiresEarlierSeconds($input_data['firesEarlierSeconds']);
        $entity->setTitle($input_data['title']);
        $entity->setContent($input_data['content']);
        $entity->setRecipientUser($recipient_user);
        $entity->setRecipientRole($recipient_role);
        $entity->setRecipientTerminOwnerUser($input_data['recipientTerminOwnerUser'] ?? false);
        $entity->setRecipientTerminOwnerRole($input_data['recipientTerminOwnerRole'] ?? false);
        $entity->setRecipientTerminOrganizer($input_data['recipientTerminOrganizer'] ?? false);
        $entity->setRecipientTerminVolunteers($input_data['recipientTerminVolunteers'] ?? false);
        $entity->setRecipientTerminParticipants($input_data['recipientTerminParticipants'] ?? false);
    }

    protected function getEntityById(int $id): TerminNotification {
        $repo = $this->entityManager()->getRepository(TerminNotification::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
    }
}
