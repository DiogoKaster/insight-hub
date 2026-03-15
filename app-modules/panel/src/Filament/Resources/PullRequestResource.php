<?php

declare(strict_types=1);

namespace InsightHub\Panel\Filament\Resources;

use BackedEnum;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use InsightHub\Panel\Filament\Resources\PullRequestResource\Pages\ListPullRequests;
use InsightHub\Panel\Filament\Resources\PullRequestResource\Pages\ViewPullRequest;
use InsightHub\Repository\Models\PullRequest;

class PullRequestResource extends Resource
{
    protected static ?string $model = PullRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CodeBracket;

    protected static ?string $tenantOwnershipRelationshipName = 'repository';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->schema([
                Group::make([
                    Section::make('Description')
                        ->schema([
                            TextEntry::make('body')
                                ->label('')
                                ->markdown()
                                ->placeholder('No description provided.')
                                ->columnSpanFull(),
                        ]),

                    Section::make('Stats')
                        ->columns(3)
                        ->schema([
                            TextEntry::make('additions')
                                ->label('Additions')
                                ->prefix('+')
                                ->color('success'),
                            TextEntry::make('deletions')
                                ->label('Deletions')
                                ->prefix('−')
                                ->color('danger'),
                            TextEntry::make('changed_files')
                                ->label('Files changed'),
                            TextEntry::make('commits_count')
                                ->label('Commits'),
                            TextEntry::make('comments_count')
                                ->label('Comments'),
                            TextEntry::make('review_comments_count')
                                ->label('Review comments'),
                        ]),

                    Section::make('Reviewers')
                        ->schema([
                            RepeatableEntry::make('reviewers')
                                ->label('')
                                ->schema([
                                    TextEntry::make('login')
                                        ->label('Reviewer')
                                        ->url(fn (Model $record): string => $record->html_url ?? '#')
                                        ->openUrlInNewTab(),
                                    TextEntry::make('pivot.state')
                                        ->label('Decision')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'APPROVED' => 'success',
                                            'CHANGES_REQUESTED' => 'danger',
                                            'DISMISSED' => 'gray',
                                            default => 'warning',
                                        }),
                                ])
                                ->columns(2),
                        ]),
                ])->columnSpan(2),

                Group::make([
                    Section::make('Details')
                        ->schema([
                            TextEntry::make('number')
                                ->label('Number')
                                ->prefix('#'),
                            TextEntry::make('state')
                                ->badge()
                                ->color(fn (PullRequest $record): string => match (true) {
                                    $record->merged_at !== null => 'purple',
                                    $record->state === 'open' => 'success',
                                    default => 'danger',
                                })
                                ->formatStateUsing(fn (PullRequest $record): string => $record->merged_at !== null ? 'merged' : $record->state),
                            TextEntry::make('html_url')
                                ->label('GitHub')
                                ->url(fn (PullRequest $record): string => $record->html_url)
                                ->formatStateUsing(fn (): string => 'View on GitHub')
                                ->openUrlInNewTab(),
                        ]),

                    Section::make('Author')
                        ->schema([
                            ImageEntry::make('author.avatar_url')
                                ->label('')
                                ->circular()
                                ->height(48),
                            TextEntry::make('author.login')
                                ->label('Login')
                                ->url(fn (PullRequest $record): string => $record->author->html_url ?? '#')
                                ->openUrlInNewTab(),
                        ]),

                    Section::make('Timestamps')
                        ->schema([
                            TextEntry::make('github_created_at')
                                ->label('Opened')
                                ->dateTime(),
                            TextEntry::make('merged_at')
                                ->label('Merged')
                                ->dateTime()
                                ->placeholder('—'),
                            TextEntry::make('closed_at')
                                ->label('Closed')
                                ->dateTime()
                                ->placeholder('—'),
                            TextEntry::make('github_updated_at')
                                ->label('Last updated')
                                ->since(),
                        ]),
                ])->columnSpan(1),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('github_created_at', 'desc')
            ->columns([
                TextColumn::make('number')
                    ->label('#')
                    ->sortable()
                    ->prefix('#'),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(60),
                TextColumn::make('author.login')
                    ->label('Author')
                    ->sortable(),
                TextColumn::make('state')
                    ->badge()
                    ->color(fn (PullRequest $record): string => match (true) {
                        $record->merged_at !== null => 'purple',
                        $record->state === 'open' => 'success',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn (PullRequest $record): string => match (true) {
                        $record->merged_at !== null => 'merged',
                        default => $record->state,
                    }),
                IconColumn::make('draft')
                    ->boolean()
                    ->trueIcon(Heroicon::PencilSquare)
                    ->falseIcon(''),
                TextColumn::make('additions')
                    ->label('+')
                    ->color('success')
                    ->sortable(),
                TextColumn::make('deletions')
                    ->label('−')
                    ->color('danger')
                    ->sortable(),
                TextColumn::make('changed_files')
                    ->label('Files')
                    ->sortable(),
                TextColumn::make('github_created_at')
                    ->label('Opened')
                    ->since()
                    ->sortable(),
                TextColumn::make('merged_at')
                    ->label('Merged')
                    ->since()
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('state')
                    ->options([
                        'open' => 'Open',
                        'closed' => 'Closed',
                    ]),
                SelectFilter::make('draft')
                    ->options([
                        '1' => 'Draft',
                        '0' => 'Ready',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPullRequests::route('/'),
            'view' => ViewPullRequest::route('/{record}'),
        ];
    }
}
