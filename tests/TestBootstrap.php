<?php declare(strict_types=1);

// phpcs:ignoreFile

use MatheusGontijo\SystemConfigHistory\Tests\TestBootstrapper;

require __DIR__ . '/TestBootstrapper.php';
require __DIR__ . '/TestDefaults.php';

return (new TestBootstrapper())
    ->setPlatformEmbedded(false)
    ->setForceInstallPlugins(true)
    ->addActivePlugins('MatheusGontijoSystemConfigHistory', 'MatheusGontijoSystemConfigHistoryTestSetup')
    ->addCallingPlugin()
    ->bootstrap();
