<?php

declare(strict_types=1);

namespace InsightHub\Admin\Filament\Resources\RepositoryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use InsightHub\Admin\Filament\Resources\RepositoryResource;

class CreateRepository extends CreateRecord
{
    protected static string $resource = RepositoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        [$owner, $name] = explode('/', (string) $data['full_name'], 2);
        $data['owner_login'] = $owner;
        $data['name'] = $name;

        return $data;
    }
}
