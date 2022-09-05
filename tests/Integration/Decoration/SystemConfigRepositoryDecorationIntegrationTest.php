<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Integration\Model;

use MatheusGontijo\SystemConfigHistory\Decoration\SystemConfigRepositoryDecoration;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;

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
