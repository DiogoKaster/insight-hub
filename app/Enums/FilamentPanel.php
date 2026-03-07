<?php

declare(strict_types=1);

namespace App\Enums;

enum FilamentPanel: string
{
    case Admin = 'admin';
    case App = 'app';

    public function getPath(): string
    {
        return match ($this) {
            self::Admin => 'admin',
            self::App => '',
        };
    }
}
