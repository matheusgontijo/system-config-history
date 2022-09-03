<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Unit\Decoration;

use Doctrine\Common\Collections\ArrayCollection;
use MatheusGontijo\SystemConfigHistory\Model\RequestStateRegistry;
use MatheusGontijo\SystemConfigHistory\Model\SystemConfigRepositoryDecorationProcess;
use MatheusGontijo\SystemConfigHistory\Decoration\SystemConfigRepositoryDecoration;
use MatheusGontijo\SystemConfigHistory\Repository\Decoration\SystemConfigRepositoryDecorationRepository;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEventFactory;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResultCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Write\CloneBehavior;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SystemConfig\SystemConfigEntity;
use Shopware\Core\System\User\UserEntity;
use Symfony\Component\HttpFoundation\Request;

/**
 * @TODO: TEST ENABLED/DISABLED
 */
class SystemConfigRepositoryDecorationUnitTest extends TestCase
{
    public function testSetEntityLoadedEventFactory(): void
    {
        $entityRepositoryMock = $this->createMock(EntityRepository::class);
        $systemConfigRepositoryDecorationProcessMock = $this->createMock(
            SystemConfigRepositoryDecorationProcess::class
        );
        $systemConfigRepositoryDecorationRepository = $this->createMock(
            SystemConfigRepositoryDecorationRepository::class
        );
        $entityLoadedEventFactoryMock = $this->createMock(EntityLoadedEventFactory::class);

        $entityRepositoryMock->expects(static::exactly(1))
            ->method('setEntityLoadedEventFactory')
            ->withConsecutive([$entityLoadedEventFactoryMock]);

        $systemConfigRepositoryDecoration = new SystemConfigRepositoryDecoration(
            $entityRepositoryMock,
            $systemConfigRepositoryDecorationProcessMock,
            $systemConfigRepositoryDecorationRepository
        );

        $systemConfigRepositoryDecoration->setEntityLoadedEventFactory($entityLoadedEventFactoryMock);
    }

    public function testGetDefinition(): void
    {
        $entityRepositoryMock = $this->createMock(EntityRepository::class);
        $systemConfigRepositoryDecorationProcessMock = $this->createMock(
            SystemConfigRepositoryDecorationProcess::class
        );
        $systemConfigRepositoryDecorationRepository = $this->createMock(
            SystemConfigRepositoryDecorationRepository::class
        );
        $entityDefinitionMock = $this->createMock(EntityDefinition::class);

        $entityRepositoryMock->expects(static::exactly(1))
            ->method('getDefinition')
            ->willReturn($entityDefinitionMock);

        $systemConfigRepositoryDecoration = new SystemConfigRepositoryDecoration(
            $entityRepositoryMock,
            $systemConfigRepositoryDecorationProcessMock,
            $systemConfigRepositoryDecorationRepository
        );

        static::assertSame(
            $entityDefinitionMock,
            $systemConfigRepositoryDecoration->getDefinition()
        );
    }

    public function testSearch(): void
    {
        $entityRepositoryMock = $this->createMock(EntityRepository::class);
        $systemConfigRepositoryDecorationProcessMock = $this->createMock(
            SystemConfigRepositoryDecorationProcess::class
        );
        $systemConfigRepositoryDecorationRepository = $this->createMock(
            SystemConfigRepositoryDecorationRepository::class
        );
        $criteriaMock = $this->createMock(Criteria::class);
        $contextMock = $this->createMock(Context::class);
        $entitySearchResultMock = $this->createMock(EntitySearchResult::class);

        $entityRepositoryMock->expects(static::exactly(1))
            ->method('search')
            ->withConsecutive([$criteriaMock, $contextMock])
            ->willReturn($entitySearchResultMock);

        $systemConfigRepositoryDecoration = new SystemConfigRepositoryDecoration(
            $entityRepositoryMock,
            $systemConfigRepositoryDecorationProcessMock,
            $systemConfigRepositoryDecorationRepository
        );

        static::assertSame(
            $entitySearchResultMock,
            $systemConfigRepositoryDecoration->search($criteriaMock, $contextMock)
        );
    }

    public function testAggregate(): void
    {
        $entityRepositoryMock = $this->createMock(EntityRepository::class);
        $systemConfigRepositoryDecorationProcessMock = $this->createMock(
            SystemConfigRepositoryDecorationProcess::class
        );
        $systemConfigRepositoryDecorationRepository = $this->createMock(
            SystemConfigRepositoryDecorationRepository::class
        );
        $criteriaMock = $this->createMock(Criteria::class);
        $contextMock = $this->createMock(Context::class);
        $aggregationResultCollectionMock = $this->createMock(AggregationResultCollection::class);

        $entityRepositoryMock->expects(static::exactly(1))
            ->method('aggregate')
            ->withConsecutive([$criteriaMock, $contextMock])
            ->willReturn($aggregationResultCollectionMock);

        $systemConfigRepositoryDecoration = new SystemConfigRepositoryDecoration(
            $entityRepositoryMock,
            $systemConfigRepositoryDecorationProcessMock,
            $systemConfigRepositoryDecorationRepository
        );

        static::assertSame(
            $aggregationResultCollectionMock,
            $systemConfigRepositoryDecoration->aggregate($criteriaMock, $contextMock)
        );
    }

    public function testSearchIds(): void
    {
        $entityRepositoryMock = $this->createMock(EntityRepository::class);
        $systemConfigRepositoryDecorationProcessMock = $this->createMock(
            SystemConfigRepositoryDecorationProcess::class
        );
        $systemConfigRepositoryDecorationRepository = $this->createMock(
            SystemConfigRepositoryDecorationRepository::class
        );
        $criteriaMock = $this->createMock(Criteria::class);
        $contextMock = $this->createMock(Context::class);
        $idSearchResultMock = $this->createMock(IdSearchResult::class);

        $entityRepositoryMock->expects(static::exactly(1))
            ->method('searchIds')
            ->withConsecutive([$criteriaMock, $contextMock])
            ->willReturn($idSearchResultMock);

        $systemConfigRepositoryDecoration = new SystemConfigRepositoryDecoration(
            $entityRepositoryMock,
            $systemConfigRepositoryDecorationProcessMock,
            $systemConfigRepositoryDecorationRepository
        );

        static::assertSame(
            $idSearchResultMock,
            $systemConfigRepositoryDecoration->searchIds($criteriaMock, $contextMock)
        );
    }

    public function testUpdate(): void
    {
        $entityRepositoryMock = $this->createMock(EntityRepository::class);
        $systemConfigRepositoryDecorationProcessMock = $this->createMock(
            SystemConfigRepositoryDecorationProcess::class
        );
        $systemConfigRepositoryDecorationRepository = $this->createMock(
            SystemConfigRepositoryDecorationRepository::class
        );
        $contextMock = $this->createMock(Context::class);
        $entityWrittenContainerEventMock = $this->createMock(EntityWrittenContainerEvent::class);

        $call = fn () => $entityWrittenContainerEventMock;

        $systemConfigRepositoryDecorationProcessMock->expects(static::exactly(1))
            ->method('process')
            ->withConsecutive([$call, []])
            ->willReturn($entityWrittenContainerEventMock);

        $systemConfigRepositoryDecoration = new SystemConfigRepositoryDecoration(
            $entityRepositoryMock,
            $systemConfigRepositoryDecorationProcessMock,
            $systemConfigRepositoryDecorationRepository
        );

        static::assertEquals(
            $entityWrittenContainerEventMock,
            $systemConfigRepositoryDecoration->update([], $contextMock)
        );
    }

    public function testUpsert(): void
    {
        $entityRepositoryMock = $this->createMock(EntityRepository::class);
        $systemConfigRepositoryDecorationProcessMock = $this->createMock(
            SystemConfigRepositoryDecorationProcess::class
        );
        $systemConfigRepositoryDecorationRepository = $this->createMock(
            SystemConfigRepositoryDecorationRepository::class
        );
        $contextMock = $this->createMock(Context::class);
        $entityWrittenContainerEventMock = $this->createMock(EntityWrittenContainerEvent::class);

        $call = fn () => $entityWrittenContainerEventMock;

        $systemConfigRepositoryDecorationProcessMock->expects(static::exactly(1))
            ->method('process')
            ->withConsecutive([$call, []])
            ->willReturn($entityWrittenContainerEventMock);

        $systemConfigRepositoryDecoration = new SystemConfigRepositoryDecoration(
            $entityRepositoryMock,
            $systemConfigRepositoryDecorationProcessMock,
            $systemConfigRepositoryDecorationRepository
        );

        static::assertEquals(
            $entityWrittenContainerEventMock,
            $systemConfigRepositoryDecoration->upsert([], $contextMock)
        );
    }

    public function testCreate(): void
    {
        $entityRepositoryMock = $this->createMock(EntityRepository::class);
        $systemConfigRepositoryDecorationProcessMock = $this->createMock(
            SystemConfigRepositoryDecorationProcess::class
        );
        $systemConfigRepositoryDecorationRepository = $this->createMock(
            SystemConfigRepositoryDecorationRepository::class
        );
        $contextMock = $this->createMock(Context::class);
        $entityWrittenContainerEventMock = $this->createMock(EntityWrittenContainerEvent::class);

        $call = fn () => $entityWrittenContainerEventMock;

        $systemConfigRepositoryDecorationProcessMock->expects(static::exactly(1))
            ->method('process')
            ->withConsecutive([$call, []])
            ->willReturn($entityWrittenContainerEventMock);

        $systemConfigRepositoryDecoration = new SystemConfigRepositoryDecoration(
            $entityRepositoryMock,
            $systemConfigRepositoryDecorationProcessMock,
            $systemConfigRepositoryDecorationRepository
        );

        static::assertEquals(
            $entityWrittenContainerEventMock,
            $systemConfigRepositoryDecoration->create([], $contextMock)
        );
    }

    public function testDelete(): void
    {
        $entityRepositoryMock = $this->createMock(EntityRepository::class);
        $systemConfigRepositoryDecorationProcessMock = $this->createMock(
            SystemConfigRepositoryDecorationProcess::class
        );
        $systemConfigRepositoryDecorationRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationRepository::class
        );
        $contextMock = $this->createMock(Context::class);
        $entityWrittenContainerEventMock = $this->createMock(EntityWrittenContainerEvent::class);

        $ids = [
            '76379098989c43ef91acbdeb53249857',
            '5b22b58b37e04199b5219c752bc316fb',
            '5b6d32a715364cc5a1ac0673e28c0228',
        ];

        $systemConfig1 = new SystemConfigEntity();
        $systemConfig1->setConfigurationKey('my.custom.configKey1');
        $systemConfig1->setSalesChannelId(null);

        $systemConfig2 = new SystemConfigEntity();
        $systemConfig2->setConfigurationKey('my.custom.configKey2');
        $systemConfig2->setSalesChannelId(null);

        $systemConfigs = [$systemConfig1, $systemConfig2];

        $systemConfigRepositoryDecorationRepositoryMock->expects(static::exactly(1))
            ->method('search')
            ->withConsecutive([$entityRepositoryMock, $ids])
            ->willReturnOnConsecutiveCalls($systemConfigs);

        $call = fn () => $entityWrittenContainerEventMock;

        $data = [
            [
                'configurationKey' => 'my.custom.configKey1',
                'salesChannelId' => null,
            ],
            [
                'configurationKey' => 'my.custom.configKey2',
                'salesChannelId' => null,
            ],
        ];

        $systemConfigRepositoryDecorationProcessMock->expects(static::exactly(1))
            ->method('process')
            ->withConsecutive([$call, $data])
            ->willReturn($entityWrittenContainerEventMock);

        $systemConfigRepositoryDecoration = new SystemConfigRepositoryDecoration(
            $entityRepositoryMock,
            $systemConfigRepositoryDecorationProcessMock,
            $systemConfigRepositoryDecorationRepositoryMock
        );

        $ids = [
            ['id' => '76379098989c43ef91acbdeb53249857'],
            ['id' => '5b22b58b37e04199b5219c752bc316fb'],
            ['id' => '5b6d32a715364cc5a1ac0673e28c0228'],
        ];

        static::assertEquals(
            $entityWrittenContainerEventMock,
            $systemConfigRepositoryDecoration->delete($ids, $contextMock)
        );
    }

    public function testCreateVersion(): void
    {
        $entityRepositoryMock = $this->createMock(EntityRepository::class);
        $systemConfigRepositoryDecorationProcessMock = $this->createMock(
            SystemConfigRepositoryDecorationProcess::class
        );
        $systemConfigRepositoryDecorationRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationRepository::class
        );

        $entityRepositoryMock->expects(static::exactly(1))
            ->method('createVersion')
            ->withConsecutive([
                '45b0fe46da4c4163b52ac0efd60f17e6',
                Context::createDefaultContext(),
                'foo',
                '6e671d7b9cd94af8b4fe3df257d1a130'
            ])
            ->willReturn('ca9619937e694143bcff4049bcc29ae3');

        $systemConfigRepositoryDecoration = new SystemConfigRepositoryDecoration(
            $entityRepositoryMock,
            $systemConfigRepositoryDecorationProcessMock,
            $systemConfigRepositoryDecorationRepositoryMock
        );

        static::assertEquals(
            'ca9619937e694143bcff4049bcc29ae3',
            $systemConfigRepositoryDecoration->createVersion(
                '45b0fe46da4c4163b52ac0efd60f17e6',
                Context::createDefaultContext(),
                'foo',
                '6e671d7b9cd94af8b4fe3df257d1a130'
            )
        );
    }

    public function testMerge(): void
    {
        $entityRepositoryMock = $this->createMock(EntityRepository::class);
        $systemConfigRepositoryDecorationProcessMock = $this->createMock(
            SystemConfigRepositoryDecorationProcess::class
        );
        $systemConfigRepositoryDecorationRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationRepository::class
        );

        $entityRepositoryMock->expects(static::exactly(1))
            ->method('merge')
            ->withConsecutive(['45b0fe46da4c4163b52ac0efd60f17e6', Context::createDefaultContext()]);

        $systemConfigRepositoryDecoration = new SystemConfigRepositoryDecoration(
            $entityRepositoryMock,
            $systemConfigRepositoryDecorationProcessMock,
            $systemConfigRepositoryDecorationRepositoryMock
        );

        $systemConfigRepositoryDecoration->merge('45b0fe46da4c4163b52ac0efd60f17e6', Context::createDefaultContext());
    }

    public function testClone(): void
    {
        $entityRepositoryMock = $this->createMock(EntityRepository::class);
        $systemConfigRepositoryDecorationProcessMock = $this->createMock(
            SystemConfigRepositoryDecorationProcess::class
        );
        $systemConfigRepositoryDecorationRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationRepository::class
        );
        $entityWrittenContainerEventMock = $this->createMock(EntityWrittenContainerEvent::class);
        $cloneBehaviorMock = $this->createMock(CloneBehavior::class);

        $entityRepositoryMock->expects(static::exactly(1))
            ->method('clone')
            ->withConsecutive([
                '45b0fe46da4c4163b52ac0efd60f17e6',
                Context::createDefaultContext(),
                '50e99c4b393e450fbdb3fda0364b6517',
                $cloneBehaviorMock
            ])
            ->willReturnOnConsecutiveCalls($entityWrittenContainerEventMock);

        $systemConfigRepositoryDecoration = new SystemConfigRepositoryDecoration(
            $entityRepositoryMock,
            $systemConfigRepositoryDecorationProcessMock,
            $systemConfigRepositoryDecorationRepositoryMock
        );

        static::assertSame(
            $entityWrittenContainerEventMock,
            $systemConfigRepositoryDecoration->clone(
                '45b0fe46da4c4163b52ac0efd60f17e6',
                Context::createDefaultContext(),
                '50e99c4b393e450fbdb3fda0364b6517',
                $cloneBehaviorMock
            )
        );
    }
}
