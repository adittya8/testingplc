<?php

use App\Http\Middleware\DataSharer;
use App\Http\Middleware\SetLocale;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'share' => DataSharer::class,
            'locale' => SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {})
    ->withSchedule(function (Schedule $schedule) {
        // $schedule->command(SendScheduleCommand::class)->everyMinute();
    })
    ->create();
