<?php

declare(strict_types=1);

return [
    /**
     * Number of days to retain mail log entries. The built-in prune command
     * (php artisan mail-log:prune) deletes entries older than this.
     */
    'prune_days' => (int) env('MAIL_LOG_PRUNE_DAYS', 365),

    /**
     * Eloquent model used by the package. Override this with your own subclass
     * to add custom behaviour, scopes, or relationships.
     */
    'model' => CodelDev\LaravelMailLog\Models\LaravelMailLog::class,

    /**
     *  Database table used by the package to store sent records. Change this if the
     *  default conflicts with existing tables in your application.
     */
    'table' => env('MAIL_LOG_TABLE', 'mail_log'),
];
