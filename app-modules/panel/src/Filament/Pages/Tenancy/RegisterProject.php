<?php

declare(strict_types=1);

namespace InsightHub\Panel\Filament\Pages\Tenancy;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use InsightHub\Project\Models\Project;

class RegisterProject extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Create Project';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required(),
            Textarea::make('description'),
        ]);
    }

    protected function handleRegistration(array $data): Project
    {
        return Project::create($data);
    }
}
