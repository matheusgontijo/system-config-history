<?php declare(strict_types=1);

// phpcs:ignoreFile

use MatheusGontijo\SystemConfigHistory\Tests\TestBootstrapper;

require __DIR__ . '/TestDefaults.php';
require __DIR__ . '/TestBootstrapper.php';

return (new TestBootstrapper())
    ->setPlatformEmbedded(false)
    ->setDatabaseUrl(getenv('DATABASE_URL'))
    ->bootstrap();
