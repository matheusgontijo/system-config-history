<?php declare(strict_types=1);

// phpcs:ignoreFile

use Shopware\Core\TestBootstrapper;

require __DIR__ . '/../../../../vendor/shopware/platform/src/Core/TestBootstrapper.php';

return (new TestBootstrapper())
    ->setPlatformEmbedded(false)
    ->setForceInstallPlugins(true)
    ->addActivePlugins('MatheusGontijoSystemConfigHistory')
    ->bootstrap();
