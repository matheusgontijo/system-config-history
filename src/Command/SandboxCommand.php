<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Command;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

class SandboxCommand extends Command
{
    protected static $defaultName = 'sandbox';

    private EntityRepositoryInterface $matheusGontijoSystemConfigHistoryRepository;

    public function __construct(EntityRepositoryInterface $matheusGontijoSystemConfigHistoryRepository, string $name = null)
    {
        parent::__construct($name);
        $this->matheusGontijoSystemConfigHistoryRepository = $matheusGontijoSystemConfigHistoryRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        for ($i = 0; $i <= 1000; $i++) {
            echo $i . PHP_EOL;
            $salesChannelId = null;

            if ($i % 3 === 0) {
                $salesChannelId = '81e29868e1794db9a0dd4fcd213b08bf';
            }

            if ($i % 10 === 0) {
                $salesChannelId = '98432def39fc4624b33213a56b8c944d';
            }

            $data = [
                'id' => Uuid::randomHex(),
                'configurationKey' => 'core.address.' . Uuid::randomHex(),
                'configurationValueOld' => ['_value' => Uuid::randomHex()],
                'configurationValueNew' => ['_value' => Uuid::randomHex()],
                'salesChannelId' => $salesChannelId,
                'username' => 'mgontijo',
                'userData' => null,
                'actionType' => 'admin',
            ];

            $this->matheusGontijoSystemConfigHistoryRepository->create([$data], Context::createDefaultContext());
        }

        return Command::SUCCESS;
    }
}
