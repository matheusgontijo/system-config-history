<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Repository;

use MatheusGontijo\SystemConfigHistory\Model\SystemConfigRepositoryDecorationProcess;
use Shopware\Core\Framework\Context;
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
use Shopware\Core\System\SystemConfig\SystemConfigEntity;

/**
 * @psalm-suppress ConstructorSignatureMismatch
 * @psalm-suppress DeprecatedMethod
 * @psalm-suppress ImplementedParamTypeMismatch
 * @psalm-suppress InvalidExtendClass
 * @psalm-suppress MethodSignatureMismatch
 * @psalm-suppress MissingClosureReturnType
 * @psalm-suppress MissingImmutableAnnotation
 */
class SystemConfigRepositoryDecoration extends EntityRepository
{
    private EntityRepository $entityRepository;

    private SystemConfigRepositoryDecorationProcess $systemConfigRepositoryDecorationProcess;

    /**
     * @inerhitDoc
     */
    public function __construct(
        EntityRepository $entityRepository,
        SystemConfigRepositoryDecorationProcess $systemConfigRepositoryDecorationProcess
    ) {
        $this->entityRepository = $entityRepository;
        $this->systemConfigRepositoryDecorationProcess = $systemConfigRepositoryDecorationProcess;
    }

    /**
     * @inerhitDoc
     */
    public function setEntityLoadedEventFactory(EntityLoadedEventFactory $eventFactory): void
    {
        $this->entityRepository->setEntityLoadedEventFactory($eventFactory);
    }

    public function getDefinition(): EntityDefinition
    {
        return $this->entityRepository->getDefinition();
    }

    public function search(Criteria $criteria, Context $context): EntitySearchResult
    {
        return $this->entityRepository->search($criteria, $context);
    }

    public function aggregate(Criteria $criteria, Context $context): AggregationResultCollection
    {
        return $this->entityRepository->aggregate($criteria, $context);
    }

    public function searchIds(Criteria $criteria, Context $context): IdSearchResult
    {
        return $this->entityRepository->searchIds($criteria, $context);
    }

    /**
     * @param array<mixed> $data
     */
    public function update(array $data, Context $context): EntityWrittenContainerEvent
    {
        $call = fn () => $this->entityRepository->update($data, $context);

        return $this->systemConfigRepositoryDecorationProcess->process($call, $data);
    }

    /**
     * @param array<mixed> $data
     */
    public function upsert(array $data, Context $context): EntityWrittenContainerEvent
    {
        $call = fn () => $this->entityRepository->upsert($data, $context);

        return $this->systemConfigRepositoryDecorationProcess->process($call, $data);
    }

    /**
     * @param array<mixed> $data
     */
    public function create(array $data, Context $context): EntityWrittenContainerEvent
    {
        $call = fn () => $this->entityRepository->create($data, $context);

        return $this->systemConfigRepositoryDecorationProcess->process($call, $data);
    }

    /**
     * @param array<mixed> $ids
     */
    public function delete(array $ids, Context $context): EntityWrittenContainerEvent
    {
        $searchIds = [];

        foreach ($ids as $id) {
            $searchIds[] = $id['id'];
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('id', $searchIds));

        $systemConfigSearchResult = $this->entityRepository->search($criteria, Context::createDefaultContext());

        $data = [];

        foreach ($systemConfigSearchResult as $systemConfig) {
            \assert($systemConfig instanceof SystemConfigEntity);

            $data[] = [
                'configurationKey' => $systemConfig->getConfigurationKey(),
                'salesChannelId' => $systemConfig->getSalesChannelId(),
            ];
        }

        $call = fn () => $this->entityRepository->delete($ids, $context);

        return $this->systemConfigRepositoryDecorationProcess->process($call, $data);
    }

    public function createVersion(string $id, Context $context, ?string $name = null, ?string $versionId = null): string
    {
        return $this->entityRepository->createVersion($id, $context, $name, $versionId);
    }

    public function merge(string $versionId, Context $context): void
    {
        $this->entityRepository->merge($versionId, $context);
    }

    public function clone(
        string $id,
        Context $context,
        ?string $newId = null,
        ?CloneBehavior $behavior = null
    ): EntityWrittenContainerEvent {
        return $this->entityRepository->clone($id, $context, $newId, $behavior);
    }
}
