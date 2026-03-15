<?php

declare(strict_types=1);

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Queue;
use InsightHub\Admin\Filament\Resources\RepositoryResource\Pages\ListRepositories;
use InsightHub\Repository\Jobs\SyncRepositoryJob;
use InsightHub\Repository\Models\Repository;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Queue::fake();
});

it('has a sync action on the repository table', function (): void {
    $repository = Repository::factory()->create();

    Livewire::test(ListRepositories::class)
        ->assertTableActionExists('sync', record: $repository);
});

it('dispatches SyncRepositoryJob when sync action is confirmed', function (): void {
    $repository = Repository::factory()->create();
    Queue::fake(); // reset after factory creation

    Livewire::test(ListRepositories::class)
        ->callTableAction('sync', record: $repository);

    Queue::assertPushed(SyncRepositoryJob::class, fn (SyncRepositoryJob $job): bool => $job->repository->is($repository));
});
