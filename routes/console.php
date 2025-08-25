<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

if (!app()->environment('testing')) {
    // Planifier la déduction automatique des crédits du personnel chaque jour à 23:50
    Schedule::command('credits:deduire-personnel')->dailyAt('23:50');
}

// Planifier la déduction automatique des crédits du personnel chaque jour à 23:50
// Si 'auto_deduct' est désactivé, la commande sortira immédiatement sans action
Schedule::command('credits:deduire-personnel')->dailyAt('23:50');


// Planifier la déduction automatique des crédits du personnel chaque jour à 23:50
// Si 'auto_deduct' est désactivé, la commande sortira immédiatement sans action
Schedule::command('credits:deduire-personnel')->dailyAt('23:50');
