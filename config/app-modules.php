<?php

declare(strict_types=1);

use Tests\TestCase;

return [
    'modules_namespace' => 'InsightHub',
    'modules_vendor' => 'insighthub',
    'modules_directory' => 'app-modules',
    'tests_base' => TestCase::class,
    'should_discover_events' => null,
    'stubs' => [
        'composer.json' => base_path('stubs/app-modules/composer-stub.json'),
        'phpstan.neon' => base_path('stubs/app-modules/phpstan.neon'),
        'phpstan.ignore.neon' => base_path('stubs/app-modules/phpstan.ignore.neon'),
        'src/Providers/StubClassNamePrefixServiceProvider.php' => base_path('stubs/app-modules/ServiceProvider.php'),
        'tests/Unit/.gitkeep' => base_path('stubs/app-modules/.gitkeep'),
        'tests/Feature/.gitkeep' => base_path('stubs/app-modules/.gitkeep'),
        'database/factories/.gitkeep' => base_path('stubs/app-modules/.gitkeep'),
        'database/migrations/.gitkeep' => base_path('stubs/app-modules/.gitkeep'),
        'database/seeders/.gitkeep' => base_path('stubs/app-modules/.gitkeep'),
    ],
];
