<?php

declare(strict_types=1);

namespace InsightHub\Repository\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use InsightHub\Repository\Exceptions\GitHubRateLimitException;
use InsightHub\Repository\Models\Repository;
use RuntimeException;

class GitHubClient
{
    public function __construct(private readonly Repository $repository) {}

    public function pullRequests(int $page = 1): array
    {
        [$owner, $repo] = $this->ownerRepo();

        $response = $this->http()->get(sprintf('/repos/%s/%s/pulls', $owner, $repo), [
            'state' => 'all',
            'sort' => 'updated',
            'direction' => 'desc',
            'per_page' => 100,
            'page' => $page,
        ]);

        $this->assertSuccessful($response);

        return $response->json();
    }

    public function pullRequestDetail(int $number): array
    {
        [$owner, $repo] = $this->ownerRepo();

        $response = $this->http()->get(sprintf('/repos/%s/%s/pulls/%d', $owner, $repo, $number));

        $this->assertSuccessful($response);

        return $response->json();
    }

    public function pullRequestReviews(int $number): array
    {
        [$owner, $repo] = $this->ownerRepo();

        $response = $this->http()->get(sprintf('/repos/%s/%s/pulls/%d/reviews', $owner, $repo, $number), [
            'per_page' => 100,
        ]);

        $this->assertSuccessful($response);

        return $response->json();
    }

    private function assertSuccessful(Response $response): void
    {
        if ($response->successful()) {
            return;
        }

        if (in_array($response->status(), [403, 429], true) && $response->header('X-RateLimit-Remaining') === '0') {
            $resetAt = (int) $response->header('X-RateLimit-Reset');
            $retryAfter = max(60, $resetAt - Date::now()->getTimestamp());

            throw new GitHubRateLimitException($retryAfter);
        }

        throw new RuntimeException(sprintf('GitHub API error %d: %s', $response->status(), $response->body()));
    }

    private function http(): PendingRequest
    {
        $token = $this->repository->github_token ?? config('services.github.token');

        $request = Http::baseUrl('https://api.github.com')
            ->withHeaders([
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
            ]);

        if (filled($token)) {
            return $request->withToken((string) $token);
        }

        return $request;
    }

    /** @return array{string, string} */
    private function ownerRepo(): array
    {
        if ($this->repository->full_name !== null) {
            [$owner, $repo] = explode('/', $this->repository->full_name, 2);

            return [$owner, $repo];
        }

        if ($this->repository->html_url !== null) {
            $path = parse_url($this->repository->html_url, PHP_URL_PATH);
            $parts = explode('/', mb_ltrim((string) $path, '/'), 2);

            return [$parts[0], $parts[1]];
        }

        throw new RuntimeException('Repository has no full_name or html_url to derive owner/repo.');
    }
}
