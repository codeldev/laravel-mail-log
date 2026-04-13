<?php

declare(strict_types=1);

use CodelDev\LaravelMailLog\Models\LaravelMailLog;
use Illuminate\Support\Facades\DB;

it('prunes records older than the configured retention period', function (): void
{
    LaravelMailLog::factory()
        ->create(['created_at' => now()->subDays(400)]);

    LaravelMailLog::factory()
        ->create(['created_at' => now()->subDays(400)]);

    LaravelMailLog::factory()
        ->create(['created_at' => now()->subDays(10)]);

    $this->artisan('mail-log:prune')
        ->expectsOutputToContain('Pruned 2 recorded emails older than 365 days')
        ->assertSuccessful();

    expect(LaravelMailLog::count())
        ->toBe(1);
});

it('respects the configured prune_days value', function (): void
{
    config()->set('mail-log.prune_days', 30);

    LaravelMailLog::factory()
        ->create(['created_at' => now()->subDays(31)]);

    LaravelMailLog::factory()
        ->create(['created_at' => now()->subDays(10)]);

    $this->artisan('mail-log:prune')
        ->expectsOutputToContain('older than 30 days')
        ->assertSuccessful();

    expect(LaravelMailLog::count())
        ->toBe(1);
});

it('outputs zero when no records to prune', function (): void
{
    LaravelMailLog::factory()
        ->create(['created_at' => now()]);

    $this->artisan('mail-log:prune')
        ->expectsOutputToContain('Pruned 0 recorded emails')
        ->assertSuccessful();

    expect(LaravelMailLog::count())
        ->toBe(1);
});

it('defaults to 365 days when config key is missing', function (): void
{
    $config = config('mail-log');
    unset($config['prune_days']);
    config()->set('mail-log', $config);

    LaravelMailLog::factory()
        ->create(['created_at' => now()->subDays(400)]);

    $this->artisan('mail-log:prune')
        ->expectsOutputToContain('older than 365 days')
        ->assertSuccessful();

    expect(LaravelMailLog::count())
        ->toBe(0);
});

it('returns failure and reports error when an exception occurs', function (): void
{
    DB::statement('DROP TABLE IF EXISTS mail_log');

    $this->artisan('mail-log:prune')
        ->assertFailed();
});
