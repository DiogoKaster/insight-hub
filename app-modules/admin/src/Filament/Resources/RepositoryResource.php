<?php

declare(strict_types=1);

namespace InsightHub\Admin\Filament\Resources;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use InsightHub\Admin\Filament\Resources\RepositoryResource\Pages\CreateRepository;
use InsightHub\Admin\Filament\Resources\RepositoryResource\Pages\ListRepositories;
use InsightHub\Repository\Models\Repository;

class RepositoryResource extends Resource
{
    protected static ?string $model = Repository::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::BookOpen;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('full_name')
                ->label('Repository')
                ->placeholder('owner/repo')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->sortable(),
            TextColumn::make('full_name')->sortable(),
            TextColumn::make('language')->sortable(),
            TextColumn::make('stars_count')->sortable(),
            IconColumn::make('is_private')->boolean(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRepositories::route('/'),
            'create' => CreateRepository::route('/create'),
        ];
    }
}
