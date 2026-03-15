<?php

declare(strict_types=1);

namespace InsightHub\Repository\Jobs;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Date;
use InsightHub\Repository\Exceptions\GitHubRateLimitException;
use InsightHub\Repository\Models\GitHubUser;
use InsightHub\Repository\Models\PullRequest;
use InsightHub\Repository\Models\Repository;
use InsightHub\Repository\Services\GitHubClient;

class SyncPullRequestsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public int $tries = 5;

    public int $backoff = 30;

    public function __construct(
        public readonly Repository $repository,
        public readonly int $page = 1,
        public readonly ?CarbonInterface $cutoff = null,
    ) {}

    public function handle(): void
    {
        try {
            $this->sync();
        } catch (GitHubRateLimitException $gitHubRateLimitException) {
            $this->release($gitHubRateLimitException->retryAfter);
        }
    }

    /** @return array<int, WithoutOverlapping> */
    public function middleware(): array
    {
        return [new WithoutOverlapping(sprintf('pr-sync-%s-%d', $this->repository->id, $this->page))];
    }

    private function sync(): void
    {
        $cutoff = $this->cutoff ?? now()->subDays(90);
        $data = new GitHubClient($this->repository)->pullRequests($this->page);

        foreach ($data as $pr) {
            $author = GitHubUser::updateOrCreate(
                ['github_id' => $pr['user']['id']],
                [
                    'login' => $pr['user']['login'],
                    'avatar_url' => $pr['user']['avatar_url'] ?? null,
                    'html_url' => $pr['user']['html_url'] ?? null,
                    'type' => $pr['user']['type'] ?? 'User',
                ]
            );

            $pullRequest = PullRequest::updateOrCreate(
                ['repository_id' => $this->repository->id, 'number' => $pr['number']],
                [
                    'github_user_id' => $author->id,
                    'github_id' => $pr['id'],
                    'title' => $pr['title'],
                    'body' => $pr['body'] ?? null,
                    'state' => $pr['state'],
                    'html_url' => $pr['html_url'],
                    'merged_at' => isset($pr['merged_at']) ? Date::parse($pr['merged_at']) : null,
                    'closed_at' => isset($pr['closed_at']) ? Date::parse($pr['closed_at']) : null,
                    'github_created_at' => Date::parse($pr['created_at']),
                    'github_updated_at' => Date::parse($pr['updated_at']),
                ]
            );

            dispatch(new SyncPullRequestDetailsJob($pullRequest));
        }

        if ($data === []) {
            return;
        }

        $lastUpdated = Date::parse(end($data)['updated_at'] ?? null);
        if (count($data) === 100 && $lastUpdated->gte($cutoff)) {
            dispatch(new static($this->repository, $this->page + 1, $cutoff));
        }
    }
}
