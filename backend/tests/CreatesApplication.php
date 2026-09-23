<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Himoya: testlar RefreshDatabase ishlatadi va bazani tozalaydi.
        // Hech qachon haqiqiy MySQL bazada ishga tushmasin.
        $connection = $app['config']->get('database.default');
        $database = $app['config']->get("database.connections.{$connection}.database");
        if ($connection !== 'sqlite' || $database !== ':memory:') {
            throw new \RuntimeException(
                "Testlar faqat in-memory SQLite'da ishlaydi (hozir: {$connection}/{$database}). "
                . 'Agar `php artisan config:cache` qilingan bo\'lsa, avval `php artisan config:clear` qiling.'
            );
        }

        return $app;
    }
}
