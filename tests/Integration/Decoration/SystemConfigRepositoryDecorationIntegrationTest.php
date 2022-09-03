<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Integration\Model;

use MatheusGontijo\SystemConfigHistory\Decoration\SystemConfigRepositoryDecoration;
use MatheusGontijo\SystemConfigHistory\Repository\Model\SystemConfigRepositoryDecorationProcessRepository;
use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity;
use MatheusGontijo\SystemConfigHistory\Tests\TestDefaults;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class SystemConfigRepositoryDecorationIntegrationTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testDecorationIsWorking(): void
    {
        $systemConfigRepository = $this->getContainer()->get('system_config.repository');
        \assert($systemConfigRepository instanceof EntityRepository);

        static::assertInstanceOf(SystemConfigRepositoryDecoration::class, $systemConfigRepository);
    }
}
