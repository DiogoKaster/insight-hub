<?php

declare(strict_types=1);

namespace App\Enums;

enum FilamentPanel: string
{
    case App = 'app';

    public function getPath(): string
    {
        return match ($this) {
            self::App => '',
        };
    }
}
