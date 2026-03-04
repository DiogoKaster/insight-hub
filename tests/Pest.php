<?php

declare(strict_types=1);

use Tests\TestCase;

pest()->extend(TestCase::class)
    ->group('unit')
    ->in('Unit');

pest()->extend(TestCase::class)
    ->group('feature')
    ->in('Feature');
