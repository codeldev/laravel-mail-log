<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use CodelDev\LaravelMailLog\Enums\MailHeaderEnum;

it('has the correct cases', function (): void
{
    expect(MailHeaderEnum::cases())->toHaveCount(4);
});

it('has the correct string values', function (MailHeaderEnum $case, string $expected): void
{
    expect($case->value)->toBe($expected);
})->with([
    [MailHeaderEnum::FROM, 'From'],
    [MailHeaderEnum::TO, 'To'],
    [MailHeaderEnum::CC, 'Cc'],
    [MailHeaderEnum::BCC, 'Bcc'],
]);

it('can be created from string values', function (string $value, MailHeaderEnum $expected): void
{
    expect(MailHeaderEnum::from($value))->toBe($expected);
})->with([
    ['From', MailHeaderEnum::FROM],
    ['To', MailHeaderEnum::TO],
    ['Cc', MailHeaderEnum::CC],
    ['Bcc', MailHeaderEnum::BCC],
]);
