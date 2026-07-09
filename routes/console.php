<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // <-- IMPORTANTE: Agrega esta línea

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Se ejecuta todos los días a las 08:00 AM
Schedule::command('cobros:generar')->dailyAt('12:00');