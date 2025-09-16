<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Forcer l'environnement de test afin d'éviter toute confirmation interactive
        config(['app.env' => 'testing']);

        return $app;
    }
}
