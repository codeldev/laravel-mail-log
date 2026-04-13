<?php

declare(strict_types=1);

namespace CodelDev\LaravelMailLog;

use CodelDev\LaravelMailLog\Commands\LaravelMailLogCommand;
use CodelDev\LaravelMailLog\Listeners\LaravelLogMailSent;
use CodelDev\LaravelMailLog\Models\LaravelMailLog;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelMailLogServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-mail-log')
            ->hasConfigFile()
            ->hasMigration('create_mail_log_table')
            ->hasCommand(LaravelMailLogCommand::class);
    }

    public function bootingPackage(): void
    {
        $this->bindModels();
        $this->registerListeners();
    }

    private function registerListeners(): void
    {
        Event::listen(MessageSending::class, LaravelLogMailSent::class);
    }

    private function bindModels(): void
    {
        /** @var class-string $model */
        $model = config('mail-log.model', LaravelMailLog::class);

        $this->app->bind(LaravelMailLog::class, $model);
    }
}
