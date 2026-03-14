<?php

declare(strict_types=1);

namespace InsightHub\Panel\Filament\Pages\Tenancy;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
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
            TextInput::make('name')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn (callable $set, ?string $state) => $set('slug', Str::slug((string) $state))),
            TextInput::make('slug')
                ->required()
                ->unique(table: 'repositories'),
            TextInput::make('html_url')
                ->label('GitHub URL')
                ->placeholder('https://github.com/owner/repo'),
            Textarea::make('description'),
            Toggle::make('is_private'),
        ]);
    }

    protected function handleRegistration(array $data): Model
    {
        return Repository::create($data);
    }
}
