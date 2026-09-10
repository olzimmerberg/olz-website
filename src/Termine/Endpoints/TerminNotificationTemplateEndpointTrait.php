<?php

namespace Olz\Termine\Endpoints;

use Olz\Entity\Roles\Role;
use Olz\Entity\Termine\TerminNotificationTemplate;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Entity\Users\User;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzTerminNotificationTemplateId int
 * @phpstan-type OlzTerminNotificationTemplateData array{
 *   terminTemplateId: int,
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
trait TerminNotificationTemplateEndpointTrait {
    use WithUtilsTrait;

    /** @return OlzTerminNotificationTemplateData */
    public function getEntityData(TerminNotificationTemplate $entity): array {
        return [
            'terminTemplateId' => $entity->getTerminTemplate()->getId() ?? 0,
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

    /** @param OlzTerminNotificationTemplateData $input_data */
    public function updateEntityWithData(TerminNotificationTemplate $entity, array $input_data): void {
        $termin_template_repo = $this->entityManager()->getRepository(TerminTemplate::class);
        $termin_template_id = $input_data['terminTemplateId'];
        $termin_template = $termin_template_repo->findOneBy(['id' => $termin_template_id]);
        $this->generalUtils()->checkNotNull($termin_template, 'TerminNotificationTemplate->terminTemplate must not be null');
        $user_repo = $this->entityManager()->getRepository(User::class);
        $recipient_user_id = $input_data['recipientUserId'] ?? null;
        $recipient_user = $user_repo->findOneBy(['id' => $recipient_user_id]);
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $recipient_role_id = $input_data['recipientRoleId'] ?? null;
        $recipient_role = $role_repo->findOneBy(['id' => $recipient_role_id]);

        $entity->setTerminTemplate($termin_template);
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

    protected function getEntityById(int $id): TerminNotificationTemplate {
        $repo = $this->entityManager()->getRepository(TerminNotificationTemplate::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
    }
}
