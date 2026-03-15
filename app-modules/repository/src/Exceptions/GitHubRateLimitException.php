<?php

declare(strict_types=1);

namespace InsightHub\Repository\Exceptions;

use RuntimeException;

class GitHubRateLimitException extends RuntimeException
{
    public function __construct(public readonly int $retryAfter)
    {
        parent::__construct(sprintf('GitHub API rate limit exceeded. Retry after %d seconds.', $retryAfter));
    }
}
