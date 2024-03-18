<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Quiz\Endpoints;

use Olz\Apps\Quiz\Endpoints\RegisterSkillCategoriesEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Apps\Quiz\Endpoints\RegisterSkillCategoriesEndpoint
 */
final class RegisterSkillCategoriesEndpointTest extends UnitTestCase {
    public function testRegisterSkillCategoriesEndpointIdent(): void {
        $endpoint = new RegisterSkillCategoriesEndpoint();
        $this->assertSame('RegisterSkillCategoriesEndpoint', $endpoint->getIdent());
    }

    public function testRegisterSkillCategoriesEndpoint(): void {
        $endpoint = new RegisterSkillCategoriesEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'skillCategories' => [
                [
                    'name' => 'Child Category 1',
                    'parentCategoryName' => 'Parent Category',
                ],
                [
                    'name' => 'Child Category 2',
                    'parentCategoryName' => 'Parent Category',
                ],
                [
                    'name' => 'Parent Category',
                    'parentCategoryName' => null,
                ],
            ],
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([
            'idByName' => [
                'Child Category 1' => 'SkillCategory:11',
                'Child Category 2' => 'SkillCategory:'.Fake\FakeEntityManager::AUTO_INCREMENT_ID,
                'Parent Category' => 'SkillCategory:'.Fake\FakeEntityManager::AUTO_INCREMENT_ID,
            ],
        ], $result);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([
            [11, 'Child Category 1', 'Parent Category'],
            [Fake\FakeEntityManager::AUTO_INCREMENT_ID, 'Child Category 2', 'Parent Category'],
            [Fake\FakeEntityManager::AUTO_INCREMENT_ID, 'Parent Category', null],
        ], array_map(
            function ($skill_category) {
                $parent_category = $skill_category->getParentCategory();
                return [
                    $skill_category->getId(),
                    $skill_category->getName(),
                    $parent_category ? $parent_category->getName() : null,
                ];
            },
            $entity_manager->persisted,
        ));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }
}
