<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Cache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Proxy\ProxyFactory;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\FilterCollection;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;
use Olz\Entity\AccessToken;
use Olz\Entity\Anmelden\Booking;
use Olz\Entity\Anmelden\Registration;
use Olz\Entity\Anmelden\RegistrationInfo;
use Olz\Entity\Anniversary\RunRecord;
use Olz\Entity\AuthRequest;
use Olz\Entity\Counter;
use Olz\Entity\Faq\Question;
use Olz\Entity\Faq\QuestionCategory;
use Olz\Entity\Karten\Karte;
use Olz\Entity\Members\Member;
use Olz\Entity\News\NewsEntry;
use Olz\Entity\NotificationSubscription;
use Olz\Entity\Quiz\Skill;
use Olz\Entity\Quiz\SkillCategory;
use Olz\Entity\Quiz\SkillLevel;
use Olz\Entity\Roles\Role;
use Olz\Entity\Service\Download;
use Olz\Entity\Service\Link;
use Olz\Entity\Snippets\Snippet;
use Olz\Entity\SolvEvent;
use Olz\Entity\Startseite\WeeklyPicture;
use Olz\Entity\StravaLink;
use Olz\Entity\TelegramLink;
use Olz\Entity\Termine\Termin;
use Olz\Entity\Termine\TerminLabel;
use Olz\Entity\Termine\TerminLocation;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Entity\Throttling;
use Olz\Entity\Users\User;
use Olz\Tests\Fake\Entity\Anmelden\FakeBookingRepository;
use Olz\Tests\Fake\Entity\Anmelden\FakeRegistrationInfoRepository;
use Olz\Tests\Fake\Entity\Anmelden\FakeRegistrationRepository;
use Olz\Tests\Fake\Entity\Anniversary\FakeRunRecordRepository;
use Olz\Tests\Fake\Entity\FakeAccessTokenRepository;
use Olz\Tests\Fake\Entity\FakeAuthRequestRepository;
use Olz\Tests\Fake\Entity\FakeCounterRepository;
use Olz\Tests\Fake\Entity\FakeNotificationSubscriptionRepository;
use Olz\Tests\Fake\Entity\FakeSolvEventRepository;
use Olz\Tests\Fake\Entity\FakeStravaLinkRepository;
use Olz\Tests\Fake\Entity\FakeTelegramLinkRepository;
use Olz\Tests\Fake\Entity\FakeThrottlingRepository;
use Olz\Tests\Fake\Entity\Faq\FakeQuestionCategoryRepository;
use Olz\Tests\Fake\Entity\Faq\FakeQuestionRepository;
use Olz\Tests\Fake\Entity\Karten\FakeKarteRepository;
use Olz\Tests\Fake\Entity\Members\FakeMemberRepository;
use Olz\Tests\Fake\Entity\News\FakeNewsRepository;
use Olz\Tests\Fake\Entity\Quiz\FakeSkillCategoryRepository;
use Olz\Tests\Fake\Entity\Quiz\FakeSkillLevelRepository;
use Olz\Tests\Fake\Entity\Quiz\FakeSkillRepository;
use Olz\Tests\Fake\Entity\Roles\FakeRoleRepository;
use Olz\Tests\Fake\Entity\Service\FakeDownloadRepository;
use Olz\Tests\Fake\Entity\Service\FakeLinkRepository;
use Olz\Tests\Fake\Entity\Snippets\FakeSnippetRepository;
use Olz\Tests\Fake\Entity\Startseite\FakeWeeklyPictureRepository;
use Olz\Tests\Fake\Entity\Termine\FakeTerminLabelRepository;
use Olz\Tests\Fake\Entity\Termine\FakeTerminLocationRepository;
use Olz\Tests\Fake\Entity\Termine\FakeTerminRepository;
use Olz\Tests\Fake\Entity\Termine\FakeTerminTemplateRepository;
use Olz\Tests\Fake\Entity\Users\FakeUserRepository;

class FakeEntityManager implements EntityManagerInterface {
    public const AUTO_INCREMENT_ID = 270;

    /** @var array<mixed> */
    public array $persisted = [];
    /** @var array<mixed> */
    public array $removed = [];
    public bool $flushed = false;
    /** @var array<mixed> */
    public array $flushed_persisted = [];
    /** @var array<mixed> */
    public array $flushed_removed = [];
    /** @var array<string, mixed> */
    public array $repositories = [];

    public function __construct() {
        $this->repositories = [
            AccessToken::class => new FakeAccessTokenRepository($this),
            AuthRequest::class => new FakeAuthRequestRepository($this),
            Booking::class => new FakeBookingRepository($this),
            Counter::class => new FakeCounterRepository($this),
            Download::class => new FakeDownloadRepository($this),
            Karte::class => new FakeKarteRepository($this),
            Link::class => new FakeLinkRepository($this),
            Member::class => new FakeMemberRepository($this),
            NewsEntry::class => new FakeNewsRepository($this),
            NotificationSubscription::class => new FakeNotificationSubscriptionRepository($this),
            Question::class => new FakeQuestionRepository($this),
            QuestionCategory::class => new FakeQuestionCategoryRepository($this),
            Registration::class => new FakeRegistrationRepository($this),
            RegistrationInfo::class => new FakeRegistrationInfoRepository($this),
            Role::class => new FakeRoleRepository($this),
            RunRecord::class => new FakeRunRecordRepository($this),
            Skill::class => new FakeSkillRepository($this),
            SkillCategory::class => new FakeSkillCategoryRepository($this),
            SkillLevel::class => new FakeSkillLevelRepository($this),
            Snippet::class => new FakeSnippetRepository($this),
            SolvEvent::class => new FakeSolvEventRepository($this),
            StravaLink::class => new FakeStravaLinkRepository($this),
            TelegramLink::class => new FakeTelegramLinkRepository($this),
            Termin::class => new FakeTerminRepository($this),
            TerminLabel::class => new FakeTerminLabelRepository($this),
            TerminLocation::class => new FakeTerminLocationRepository($this),
            TerminTemplate::class => new FakeTerminTemplateRepository($this),
            Throttling::class => new FakeThrottlingRepository($this),
            User::class => new FakeUserRepository($this),
            WeeklyPicture::class => new FakeWeeklyPictureRepository($this),
        ];
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $className
     *
     * @return EntityRepository<T>
     */
    public function getRepository($className): EntityRepository {
        $repo = $this->repositories[$className] ?? null;
        if (!$repo) {
            throw new \Exception("Repository was not mocked: {$className}");
        }
        return $repo;
    }

    public function persist($object): void {
        if (method_exists($object, 'getId') && method_exists($object, 'setId')) {
            // Simulate SQL auto-increment.
            if ($object->getId() === null) {
                $object->setId(FakeEntityManager::AUTO_INCREMENT_ID);
            }
        }
        $this->persisted[] = $object;
    }

    public function remove($object): void {
        $this->removed[] = $object;
    }

    public function flush(mixed $entity = null): void {
        $this->flushed = true;
        $this->flushed_persisted = $this->persisted;
        $this->flushed_removed = $this->removed;
    }

    public function beginTransaction(): void {
        throw new \Exception('not implemented');
    }

    public function close(): void {
        throw new \Exception('not implemented');
    }

    public function commit(): void {
        throw new \Exception('not implemented');
    }

    public function createNativeQuery(string $sql, ResultSetMapping $rsm): NativeQuery {
        throw new \Exception('not implemented');
    }

    // @phpstan-ignore-next-line
    public function createQuery($dql = ''): Query {
        throw new \Exception('not implemented');
    }

    public function createQueryBuilder(): QueryBuilder {
        throw new \Exception('not implemented');
    }

    public function getCache(): ?Cache {
        throw new \Exception('not implemented');
    }

    public function getClassMetadata($className): ClassMetadata {
        throw new \Exception('not implemented');
    }

    public function getConfiguration(): Configuration {
        throw new \Exception('not implemented');
    }

    public function getConnection(): Connection {
        throw new \Exception('not implemented');
    }

    public function getEventManager(): EventManager {
        throw new \Exception('not implemented');
    }

    public function getExpressionBuilder(): Expr {
        throw new \Exception('not implemented');
    }

    public function getFilters(): FilterCollection {
        throw new \Exception('not implemented');
    }

    public function getProxyFactory(): ProxyFactory {
        throw new \Exception('not implemented');
    }

    public function getReference($entityName, $id): ?object {
        throw new \Exception('not implemented');
    }

    public function getUnitOfWork(): UnitOfWork {
        throw new \Exception('not implemented');
    }

    public function hasFilters(): bool {
        throw new \Exception('not implemented');
    }

    public function isFiltersStateClean(): bool {
        throw new \Exception('not implemented');
    }

    public function isOpen(): bool {
        throw new \Exception('not implemented');
    }

    public function lock(
        object $entity,
        LockMode|int $lockMode,
        \DateTimeInterface|int|null $lockVersion = null,
    ): void {
        throw new \Exception('not implemented');
    }

    public function newHydrator($hydrationMode): AbstractHydrator {
        throw new \Exception('not implemented');
    }

    public function rollback(): void {
        throw new \Exception('not implemented');
    }

    public function clear(): void {
        throw new \Exception('not implemented');
    }

    public function contains(object $object): bool {
        throw new \Exception('not implemented');
    }

    public function detach(object $object): void {
        throw new \Exception('not implemented');
    }

    public function find(
        string $className,
        mixed $id,
        LockMode|int|null $lockMode = null,
        ?int $lockVersion = null,
    ): ?object {
        throw new \Exception('not implemented');
    }

    public function initializeObject(object $obj): void {
        throw new \Exception('not implemented');
    }

    // @phpstan-ignore-next-line
    public function getMetadataFactory(): ClassMetadataFactory {
        throw new \Exception('not implemented');
    }

    public function refresh($entity, LockMode|int|null $lockMode = null): void {
        throw new \Exception('not implemented');
    }

    public function wrapInTransaction(callable $func): mixed {
        throw new \Exception('not implemented');
    }

    public function isUninitializedObject(mixed $value): bool {
        throw new \Exception('not implemented');
    }
}
