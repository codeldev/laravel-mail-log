<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Carbon\CarbonImmutable;
use CodelDev\LaravelMailLog\Models\LaravelMailLog;

it('uses the table name from config', function (): void
{
    expect((new LaravelMailLog)->getTable())
        ->toBe('mail_log');
});

it('uses a custom table name from config', function (): void
{
    config()
        ->set('mail-log.table', $custom = 'custom_mail_log');

    expect((new LaravelMailLog)->getTable())
        ->toBe($custom);
});

it('uses uuid as primary key', function (): void
{
    expect(LaravelMailLog::factory()->create()->id)
        ->toBeString()
        ->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');
});

it('casts json columns to arrays', function (): void
{
    $record = LaravelMailLog::factory()->create([
        'to'  => $to  = ['to@example.com'],
        'cc'  => $cc  = ['cc@example.com'],
        'bcc' => $bcc = ['bcc@example.com'],
    ]);

    expect($record->fresh()->to)
        ->toBe($to)
        ->and($record->cc)
        ->toBe($cc)
        ->and($record->bcc)
        ->toBe($bcc);
});

it('casts timestamps to CarbonImmutable', function (): void
{
    $record = LaravelMailLog::factory()
        ->create();

    expect($record->created_at)
        ->toBeInstanceOf(CarbonImmutable::class)
        ->and($record->updated_at)
        ->toBeInstanceOf(CarbonImmutable::class);
});

it('allows nullable columns to be null', function (): void
{
    $record = LaravelMailLog::factory()->create([
        'from'        => null,
        'cc'          => null,
        'bcc'         => null,
        'headers'     => null,
        'attachments' => null,
    ]);

    expect($record->fresh()->from)
        ->toBeNull()
        ->and($record->cc)
        ->toBeNull()
        ->and($record->bcc)
        ->toBeNull()
        ->and($record->headers)
        ->toBeNull()
        ->and($record->attachments)
        ->toBeNull();
});

it('casts attachments to array', function (): void
{
    $record = LaravelMailLog::factory()
        ->withAttachments(2)
        ->create();

    expect($record->fresh()->attachments)
        ->toBeArray()
        ->toHaveCount(2);
});

it('can create records via factory', function (): void
{
    LaravelMailLog::factory()
        ->count(3)
        ->create();

    expect(LaravelMailLog::count())
        ->toBe(3);
});
