<?php

declare(strict_types=1);

namespace InsightHub\Admin\Filament\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use InsightHub\Admin\Filament\Resources\RepositoryResource\Pages\CreateRepository;
use InsightHub\Admin\Filament\Resources\RepositoryResource\Pages\EditRepository;
use InsightHub\Admin\Filament\Resources\RepositoryResource\Pages\ListRepositories;
use InsightHub\Repository\Jobs\SyncRepositoryJob;
use InsightHub\Repository\Models\Repository;

class RepositoryResource extends Resource
{
    protected static ?string $model = Repository::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BookOpen;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn (callable $set, ?string $state) => $set('slug', Str::slug((string) $state))),
            TextInput::make('slug')
                ->required()
                ->readOnly()
                ->unique(ignoreRecord: true),
            TextInput::make('html_url')
                ->label('GitHub URL')
                ->placeholder('https://github.com/owner/repo'),
            TextInput::make('github_token')
                ->label('GitHub Token')
                ->password()
                ->revealable()
                ->helperText('Required for private repositories. Needs repo scope.'),
            Textarea::make('description'),
            Toggle::make('is_private'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable(),
                TextColumn::make('full_name')->sortable(),
                TextColumn::make('language')->sortable(),
                TextColumn::make('stars_count')->sortable(),
                IconColumn::make('is_private')->boolean(),
            ])
            ->recordActions([
                Action::make('manage')
                    ->label('Manage')
                    ->icon(Heroicon::ArrowTopRightOnSquare)
                    ->url(fn (Repository $record): string => route('filament.app.pages.dashboard', ['tenant' => $record->getRouteKey()]))
                    ->openUrlInNewTab(),
                Action::make('sync')
                    ->label('Sync')
                    ->icon(Heroicon::ArrowPath)
                    ->requiresConfirmation()
                    ->modalHeading('Sync Pull Requests')
                    ->modalDescription('This will fetch all pull requests from GitHub for this repository. It may take a few minutes.')
                    ->action(fn (Repository $record) => dispatch(new SyncRepositoryJob($record)))
                    ->successNotificationTitle('Sync queued'),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRepositories::route('/'),
            'create' => CreateRepository::route('/create'),
            'edit' => EditRepository::route('/{record}/edit'),
        ];
    }
}
