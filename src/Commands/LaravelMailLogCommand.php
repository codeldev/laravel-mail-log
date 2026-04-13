<?php

declare(strict_types=1);

namespace CodelDev\LaravelMailLog\Commands;

use CodelDev\LaravelMailLog\Models\LaravelMailLog;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand(
    'mail-log:prune',
    'Prune mail log entries older than the configured retention period'
)]
/** @internal */
final class LaravelMailLogCommand extends Command
{
    public function __construct(private readonly LaravelMailLog $mailLog)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        try
        {
            /** @var int $days */
            $days = config('mail-log.prune_days', 365);

            /** @var int $deleted */
            $deleted = $this->mailLog::query()
                ->whereDate('created_at', '<', now()->subDays($days))
                ->delete();

            $this->info('Pruned ' . $deleted . ' recorded emails older than ' . $days . ' days.');

            return self::SUCCESS;
        }
        catch (Throwable $throwable)
        {
            report($throwable);

            $this->error($throwable->getMessage());

            return self::FAILURE;
        }
    }
}
