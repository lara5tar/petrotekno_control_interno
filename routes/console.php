<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programar las alertas diarias de mantenimiento
Schedule::command('alertas:enviar-diarias')
    ->dailyAt('08:00')
    ->timezone('America/Mexico_City')
    ->description('Envío diario de alertas de mantenimiento')
    ->withoutOverlapping()
    ->runInBackground();
