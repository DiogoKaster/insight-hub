<?php

declare(strict_types=1);

namespace InsightHub\Repository\Observers;

use InsightHub\Repository\Jobs\SyncRepositoryJob;
use InsightHub\Repository\Models\Repository;

class RepositoryObserver
{
    public function created(Repository $model): void
    {
        dispatch(new SyncRepositoryJob($model));
    }
}
