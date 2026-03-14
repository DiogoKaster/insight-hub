<?php

declare(strict_types=1);

namespace InsightHub\Admin\Filament\Resources\RepositoryResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use InsightHub\Admin\Filament\Resources\RepositoryResource;

class EditRepository extends EditRecord
{
    protected static string $resource = RepositoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
