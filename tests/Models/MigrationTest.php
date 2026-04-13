<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;

it('uses jsonb columns for postgresql', function (string $column): void
{
    config()->set('database.default', 'pgsql');

    $statements = Schema::getConnection()->pretend(function (): void
    {
        $migration = include __DIR__ . '/../../database/migrations/create_mail_log_table.php.stub';
        $migration->up();
    });

    expect($statements[0]['query'])
        ->toContain("\"{$column}\" jsonb");
})->with(['to', 'cc', 'bcc', 'attachments']);

it('uses json columns for non-postgresql drivers', function (string $column): void
{
    $statements = Schema::getConnection()->pretend(function (): void
    {
        $migration = include __DIR__ . '/../../database/migrations/create_mail_log_table.php.stub';
        $migration->up();
    });

    expect($statements[0]['query'])
        ->toContain("\"{$column}\" text")
        ->not->toContain("\"{$column}\" jsonb");
})->with(['to', 'cc', 'bcc', 'attachments']);
