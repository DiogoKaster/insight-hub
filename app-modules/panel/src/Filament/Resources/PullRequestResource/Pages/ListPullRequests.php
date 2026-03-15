<?php

declare(strict_types=1);

namespace InsightHub\Panel\Filament\Resources\PullRequestResource\Pages;

use Filament\Resources\Pages\ListRecords;
use InsightHub\Panel\Filament\Resources\PullRequestResource;

class ListPullRequests extends ListRecords
{
    protected static string $resource = PullRequestResource::class;
}
