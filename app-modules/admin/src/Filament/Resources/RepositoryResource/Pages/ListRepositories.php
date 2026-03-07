<?php

declare(strict_types=1);

namespace InsightHub\Admin\Filament\Resources\RepositoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use InsightHub\Admin\Filament\Resources\RepositoryResource;

class ListRepositories extends ListRecords
{
    protected static string $resource = RepositoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
