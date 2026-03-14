<?php

declare(strict_types=1);

namespace InsightHub\Repository\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use InsightHub\Repository\Models\GitHubUser;
use InsightHub\Repository\Models\PullRequest;
use InsightHub\Repository\Services\GitHubClient;

class SyncPullRequestDetailsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public int $tries = 5;

    public int $backoff = 30;

    public function __construct(public readonly PullRequest $pullRequest) {}

    public function handle(): void
    {
        $repository = $this->pullRequest->repository;
        $client = new GitHubClient($repository);

        $detail = $client->pullRequestDetail($this->pullRequest->number);

        $this->pullRequest->update([
            'draft' => $detail['draft'],
            'additions' => $detail['additions'],
            'deletions' => $detail['deletions'],
            'changed_files' => $detail['changed_files'],
            'commits_count' => $detail['commits'],
            'comments_count' => $detail['comments'],
            'review_comments_count' => $detail['review_comments'],
        ]);

        $reviews = $client->pullRequestReviews($this->pullRequest->number);

        $reviewerPivot = collect($reviews)
            ->groupBy('user.id')
            ->map(fn ($userReviews) => [
                'state' => $userReviews->sortByDesc('submitted_at')->first()['state'],
            ]);

        $reviewerIds = collect($reviews)
            ->map(fn ($r) => $r['user'])
            ->unique('id')
            ->mapWithKeys(fn ($user) => [
                GitHubUser::updateOrCreate(
                    ['github_id' => $user['id']],
                    [
                        'login' => $user['login'],
                        'avatar_url' => $user['avatar_url'] ?? null,
                        'html_url' => $user['html_url'] ?? null,
                        'type' => $user['type'] ?? 'User',
                    ]
                )->id => ['state' => $reviewerPivot[(string) $user['id']]['state']],
            ]);

        $this->pullRequest->reviewers()->sync($reviewerIds);
    }
}
