<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Repository\Decoration;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

class SystemConfigRepositoryDecorationRepository
{
    /**
     * @param array<int, string> $ids
     * @return array
     */
    public function search(EntityRepositoryInterface $systemConfigRepository, array $ids): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('id', $ids));

        $systemConfigs = [];

        $searchResult = $systemConfigRepository->search($criteria, Context::createDefaultContext());

        foreach ($searchResult as $item) {
            $systemConfigs[] = $item;
        }

        return $systemConfigs;
    }
}
