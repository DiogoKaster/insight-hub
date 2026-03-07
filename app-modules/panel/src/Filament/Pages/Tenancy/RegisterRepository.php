<?php

declare(strict_types=1);

namespace InsightHub\Panel\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use InsightHub\Repository\Models\Repository;

class RegisterRepository extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Add Repository';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('full_name')
                ->label('Repository')
                ->placeholder('owner/repo')
                ->required(),
        ]);
    }

    protected function handleRegistration(array $data): Model
    {
        [$owner, $name] = explode('/', (string) $data['full_name'], 2);
        $data['owner_login'] = $owner;
        $data['name'] = $name;

        return Repository::create($data);
    }
}
