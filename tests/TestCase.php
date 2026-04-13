<?php

declare(strict_types=1);

namespace CodelDev\LaravelMailLog\Tests;

use CodelDev\LaravelMailLog\LaravelMailLogServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected static Migration $migration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prepareMigration();

        self::$migration->up();

        $this->setFactoriesPath();
    }

    /** @param Application $app */
    public function getEnvironmentSetUp($app): void
    {
        Model::preventLazyLoading();

        $app['config']->set('database.default', 'testing');
        $app['config']->set('mail-log', require __DIR__ . '/../config/mail-log.php');
    }

    /** @param Application $app */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelMailLogServiceProvider::class,
        ];
    }

    private function prepareMigration(): void
    {
        self::$migration = include __DIR__ . '/../database/migrations/create_mail_log_table.php.stub';
    }

    private function setFactoriesPath(): void
    {
        Factory::guessFactoryNamesUsing(
            static fn (string $modelName) => 'CodelDev\\LaravelMailLog\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }
}
