<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\AppPanelProvider;
use App\Providers\FilamentServiceProvider;
use App\Providers\TelescopeServiceProvider;

return [
    AppServiceProvider::class,
    FilamentServiceProvider::class,
    AdminPanelProvider::class,
    AppPanelProvider::class,
    TelescopeServiceProvider::class,
];
