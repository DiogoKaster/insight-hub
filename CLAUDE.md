# insight-hub — AI Context

## Project Overview

**insight-hub** aggregates GitHub repository data and surfaces insights in business-friendly language. The Filament admin panel is the primary UI. Each **Repository** is the Filament multi-tenant unit — all data is scoped to the selected repository.

## Tech Stack

- **Framework:** Laravel 12 (PHP 8.4)
- **Admin UI:** Filament 5
- **Testing:** Pest 4 with `RefreshDatabase`
- **Code quality:** Pint (formatting), Rector (upgrades), PHPStan level 6
- **Module system:** `internachi/modular` — modules live in `app-modules/`

## Module Map

| Module | Package | Namespace | Purpose |
|--------|---------|-----------|---------|
| `panel` | `insighthub/panel` | `InsightHub\Panel` | Filament panel config, resources, pages, widgets |
| `repository` | `insighthub/repository` | `InsightHub\Repository` | Core data models (Repository, PullRequest, Issue, Release, Label, GitHubUser) |

## Module Structure

Each module follows this layout:
```
app-modules/<name>/
├── composer.json            # package identity + autoload
├── database/
│   └── factories/           # Eloquent factories
├── src/
│   ├── <Name>ServiceProvider.php
│   └── Models/
└── tests/
    ├── Pest.php             # extends Tests\TestCase + RefreshDatabase
    └── Models/
```

## Filament Multi-Tenancy

- **Tenant model:** `InsightHub\Repository\Models\Repository`
- Panel configured with `->tenant(Repository::class)` in `AppPanelProvider`
- URL scheme: `/app/{repository}/...`
- `App\Models\User` implements `FilamentUser` + `HasTenants`
- V1: all users can access all repositories (no per-user scoping yet)

## Database Schema

All models use **UUID v4** primary keys via `HasUuids`. Foreign keys use `foreignUuid(...)->constrained()`.

Core tables: `repositories`, `github_users`, `pull_requests`, `issues`, `releases`, `labels`

Pivot tables (no auto-increment id): `issue_assignees`, `pull_request_reviewers`, `issue_label`, `pull_request_label`

## Naming Conventions

- PHP files: `declare(strict_types=1)` always at the top
- Models: singular PascalCase (`PullRequest`, `GitHubUser`)
- Tables: snake_case plural (`pull_requests`, `github_users`)
- Factories: in `database/factories/` within each module, namespace `InsightHub\<Module>\Database\Factories`
- GitHub timestamps: prefixed `github_` (e.g. `github_created_at`) to avoid collision with Laravel's `created_at`

## Git Workflow

- Branch from `main`
- PRs required before merging
- `composer check` must pass before merge (Rector, Pint, PHPStan)

## Key Commands

```bash
composer test          # run Pest tests
composer check         # Rector + Pint + PHPStan (dry-run)
composer pint          # fix formatting
composer rector        # apply Rector transforms
php artisan migrate:fresh   # reset and re-run all migrations
```
