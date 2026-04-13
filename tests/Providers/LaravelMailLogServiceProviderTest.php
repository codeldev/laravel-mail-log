<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use CodelDev\LaravelMailLog\Commands\LaravelMailLogCommand;
use CodelDev\LaravelMailLog\Listeners\LaravelLogMailSent;
use CodelDev\LaravelMailLog\Models\LaravelMailLog;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;

it('registers the config file', function (): void
{
    expect(config('mail-log.table'))
        ->toBe('mail_log')
        ->and(config('mail-log.prune_days'))
        ->toBe(365)
        ->and(config('mail-log.model'))
        ->toBe(LaravelMailLog::class);
});

it('registers the prune command', function (): void
{
    expect($commands = Artisan::all())
        ->toHaveKey('mail-log:prune')
        ->and($commands['mail-log:prune'])
        ->toBeInstanceOf(LaravelMailLogCommand::class);
});

it('binds the model from config', function (): void
{
    expect(app(LaravelMailLog::class))
        ->toBeInstanceOf(LaravelMailLog::class);
});

it('binds a custom model when configured', function (): void
{
    expect(app()->bound(LaravelMailLog::class))
        ->toBeTrue();
});

it('registers the MessageSending listener', function (): void
{
    Event::fake([MessageSending::class]);

    Event::assertListening(
        MessageSending::class,
        LaravelLogMailSent::class,
    );
});
