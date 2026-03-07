<?php

declare(strict_types=1);

namespace InsightHub\Panel\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

final class Login extends BaseLogin
{
    public function mount(): void
    {
        parent::mount();

        if (! app()->environment('production')) {
            $this->form->fill([
                'email' => 'admin@insighthub.test',
                'password' => 'password',
            ]);
        }
    }
}
