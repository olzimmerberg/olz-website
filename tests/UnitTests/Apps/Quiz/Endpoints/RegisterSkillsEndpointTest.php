<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Quiz\Endpoints;

use Olz\Apps\Quiz\Endpoints\RegisterSkillsEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Apps\Quiz\Endpoints\RegisterSkillsEndpoint
 */
final class RegisterSkillsEndpointTest extends UnitTestCase {
    public function testRegisterSkillsEndpoint(): void {
        $endpoint = new RegisterSkillsEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'skills' => [
                [
                    'name' => 'Child Category 1 Skill',
                    'categoryIds' => ['SkillCategory:2'],
                ],
                [
                    'name' => 'Multi Category Skill',
                    'categoryIds' => ['SkillCategory:1', 'SkillCategory:3'],
                ],
                [
                    'name' => 'Parent Category Skill',
                    'categoryIds' => ['SkillCategory:1'],
                ],
            ],
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([
            'idByName' => [
                'Child Category 1 Skill' => 'Skill:11',
                'Multi Category Skill' => 'Skill:'.Fake\FakeEntityManager::AUTO_INCREMENT_ID,
                'Parent Category Skill' => 'Skill:'.Fake\FakeEntityManager::AUTO_INCREMENT_ID,
            ],
        ], $result);

        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([
            [11, 'Child Category 1 Skill', [2]],
            [Fake\FakeEntityManager::AUTO_INCREMENT_ID, 'Multi Category Skill', [1, 3]],
            [Fake\FakeEntityManager::AUTO_INCREMENT_ID, 'Parent Category Skill', [1]],
        ], array_map(
            function ($skill) {
                $categories = [];
                foreach ($skill->getCategories() as $category) {
                    $categories[] = $category->getId();
                }
                return [
                    $skill->getId(),
                    $skill->getName(),
                    $categories,
                ];
            },
            $entity_manager->persisted,
        ));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }
}
