<?php

declare(strict_types=1);

namespace InsightHub\Repository;

use Illuminate\Support\ServiceProvider;
use InsightHub\Repository\Models\Repository;
use InsightHub\Repository\Observers\RepositoryObserver;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Repository::observe(RepositoryObserver::class);
    }
}
