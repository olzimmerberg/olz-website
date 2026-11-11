<?php

declare(strict_types=1);

namespace Olz\Tests\IntegrationTests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Olz\Entity\Members\Member;
use Olz\Tests\IntegrationTests\Common\IntegrationTestCase;

/**
 * @internal
 *
 * @covers \Olz\Repository\Members\MemberRepository
 */
final class MemberRepositoryIntegrationTest extends IntegrationTestCase {
    public function testGetActiveIdents(): void {
        $this->withLockedDb(function () {
            $entity_manager = $this->getEntityManager();
            $member_repo = $entity_manager->getRepository(Member::class);

            $active_idents = $member_repo->getActiveIdents();

            $this->assertNotEmpty($active_idents);
            // Sorted, and without duplicates.
            $sorted_active_idents = $active_idents;
            sort($sorted_active_idents, SORT_STRING);
            $this->assertSame($sorted_active_idents, $active_idents);
            $this->assertCount(count($sorted_active_idents), array_unique($sorted_active_idents));
            // Only members which are not deleted are returned.
            $num_active_members = 0;
            foreach ($member_repo->findAll() as $member) {
                $is_active = in_array($member->getIdent(), $active_idents, true);
                $this->assertSame($member->getDeletedAt() === null, $is_active);
                if ($is_active) {
                    $num_active_members++;
                }
            }
            $this->assertCount($num_active_members, $active_idents);

            // A soft-deleted member is not active anymore.
            $ident_to_delete = $active_idents[0];
            $member_to_delete = $member_repo->findOneBy(['ident' => $ident_to_delete]);
            $this->assertNotNull($member_to_delete);
            $member_to_delete->setDeletedAt(new \DateTime('2020-03-13 19:30:00'));
            $entity_manager->flush();

            $active_idents_after_deletion = $member_repo->getActiveIdents();
            $this->assertNotContains($ident_to_delete, $active_idents_after_deletion);
            $this->assertCount(count($active_idents) - 1, $active_idents_after_deletion);
        });
    }

    protected function getEntityManager(): EntityManagerInterface {
        // @phpstan-ignore-next-line
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
