<?php

declare(strict_types=1);

namespace CodelDev\LaravelMailLog\Models;

use Carbon\CarbonImmutable;
use CodelDev\LaravelMailLog\Database\Factories\LaravelMailLogFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

/**
 * @property-read string $id
 * @property-read string|null $from
 * @property-read array<int, string>|null $to
 * @property-read array<int, string>|null $cc
 * @property-read array<int, string>|null $bcc
 * @property-read string $subject
 * @property-read string $body
 * @property-read string|null $headers
 * @property-read array<int, string>|null $attachments
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 */
#[UseFactory(LaravelMailLogFactory::class)]
final class LaravelMailLog extends Model
{
    /** @use HasFactory<LaravelMailLogFactory> */
    use HasFactory;

    /** @see HasUuids */
    use HasUuids;

    /** @var list<string> */
    protected $guarded = [];

    #[Override]
    public function getTable(): string
    {
        /** @var string */
        return config('mail-log.table', 'mail_log');
    }

    /** @return array<string, string> */
    #[Override]
    protected function casts(): array
    {
        return [
            'id'          => 'string',
            'from'        => 'string',
            'to'          => 'array',
            'cc'          => 'array',
            'bcc'         => 'array',
            'subject'     => 'string',
            'body'        => 'string',
            'headers'     => 'string',
            'attachments' => 'array',
            'created_at'  => 'immutable_datetime',
            'updated_at'  => 'immutable_datetime',
        ];
    }
}
