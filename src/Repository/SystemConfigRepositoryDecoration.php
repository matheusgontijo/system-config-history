<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Repository;

use MatheusGontijo\SystemConfigHistory\Model\SystemConfigRepositoryDecorationProcess;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEventFactory;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Read\EntityReaderInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntityAggregatorInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearcherInterface;
use Shopware\Core\Framework\DataAbstractionLayer\VersionManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SystemConfigRepositoryDecoration extends EntityRepository
{
    private EntityRepository $entityRepository;

    private SystemConfigRepositoryDecorationProcess $systemConfigRepositoryDecorationProcess;

    public function __construct(
        EntityRepository $entityRepository,
        SystemConfigRepositoryDecorationProcess $systemConfigRepositoryDecorationProcess,
        EntityDefinition $definition,
        EntityReaderInterface $reader,
        VersionManager $versionManager,
        EntitySearcherInterface $searcher,
        EntityAggregatorInterface $aggregator,
        EventDispatcherInterface $eventDispatcher,
        ?EntityLoadedEventFactory $eventFactory = null
    ) {
        parent::__construct(
            $definition,
            $reader,
            $versionManager,
            $searcher,
            $aggregator,
            $eventDispatcher,
            $eventFactory
        );

        $this->entityRepository = $entityRepository;
        $this->systemConfigRepositoryDecorationProcess = $systemConfigRepositoryDecorationProcess;
    }

    public function create(array $data, Context $context): EntityWrittenContainerEvent
    {
        $call = function($data) use ($context) {
            return $this->entityRepository->create($data, $context);
        };

        return $this->systemConfigRepositoryDecorationProcess->process($call, $data);
    }

    public function upsert(array $data, Context $context): EntityWrittenContainerEvent
    {
        $call = function($data) use ($context) {
            return $this->entityRepository->upsert($data, $context);
        };

        return $this->systemConfigRepositoryDecorationProcess->process($call, $data);
    }

    public function update(array $data, Context $context): EntityWrittenContainerEvent
    {
        $call = function($data) use ($context) {
            return $this->entityRepository->update($data, $context);
        };

        return $this->systemConfigRepositoryDecorationProcess->process($call, $data);
    }
}
